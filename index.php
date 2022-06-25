<?php

use App\TextToSpeech;
use App\VkApiGateway;
use App\Utility;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

//$conf = require('config/config.php');
//$vk = new VkApiGateway($conf['user_token'], $conf['group_token'], $conf['weather_token'], '5.131');

$vk = new VkApiGateway(getenv('USER_TOKEN'), getenv('ACCESS_TOKEN'), '5.131');
$util = new Utility();
$tts = new TextToSpeech(getenv('TTS_TOKEN'), $util);
$data = json_decode(file_get_contents('php://input'));

if ($data->type == 'confirmation') {
    exit(getenv('CONFIRMATION_TOKEN'));
}

$vk->SendOK();

if ($data->type == 'message_new') {
    $from_id = $data->object->message->from_id;
    $message = $data->object->message->text;
    $peer_id = $data->object->message->peer_id;
}

if ($data->type == 'message_new') {
    if(mb_strtolower($message) === 'спиздани') {
        $response = json_decode($util->curlGetRequest('https://evilinsult.com/generate_insult.php?lang=ru&type=json'), true);
        $vk->sendMessage($peer_id, iconv(mb_detect_encoding($response['insult'], mb_detect_order(), true), "UTF-8", $response['insult']));
        return;
    }
    if(mb_strtolower($message) === 'стикер') {
        $stickersArray = require_once('src/stickers.php');
        $stickerPackId = random_int(1,6);
        $stickerId = random_int($stickersArray[$stickerPackId][0],$stickersArray[$stickerPackId][1]);
        $vk->sendMessageWithSticker($peer_id, $stickerId);
        return;
    }
    if(mb_strtolower($message) === 'мем') {
        $owner_idArray = require_once('src/meme_groups.php');
        $owner_id = random_int(1,6);
        $posts = json_decode($vk->getGroupWallPosts($owner_idArray[$owner_id], 20), true);
        $ids = $util->getPostsIds($posts);
        $random_post_int = random_int(0, count($ids));
        $vk->sendMessage($peer_id, 'Держи', 'wall' . $owner_idArray[$owner_id] . '_' . $ids[$random_post_int]);
        return;
    }
    if($util->strContains(mb_strtolower($message), ['айфон', 'aйфон', 'aйфoн', 'айфoн'])) {
        $vk->sendMessage($peer_id, 'айфон говно');
        return;
    }
    if($util->strContains(mb_strtolower($message), ['андроид', 'aндроид', 'aндрoид', 'aндрoид'])) {
        $vk->sendMessage($peer_id, 'андроид топ');
        return;
    }
    if(preg_match('/(бот_новости_)[а-яё]{2,}/u', mb_strtolower($message))) {
        $news_word = explode('_', mb_strtolower($message))[2];
        $news_lang = explode('_', mb_strtolower($message))[3] ?? 'ru';
        $news = json_decode($util->curlGetRequest('https://mediametrics.ru/satellites/api/search/?',
        [
            'ac' => 'search',
            'q' => $news_word,
            'p' => 0,
            'c' => $news_lang,
            'callback' => 'JSON'
        ]), true);
        $newsString = $util->transformNews($news);
        $vk->sendMessage($peer_id, $newsString);
        return;
    }
    if(preg_match('/(бот_погода_)[а-яё]{2,}/u', mb_strtolower($message))) {
        $city_name = explode('_', mb_strtolower($message))[2];
        $city_coordinates = json_decode($util->curlGetRequest('http://api.openweathermap.org/geo/1.0/direct?',
        [
            'q' => $city_name,
            'limit' => 1,
            'appid' => getenv('WEATHER_TOKEN')
        ]), true);
        $daily_temp = json_decode($util->curlGetRequest('https://api.openweathermap.org/data/2.5/onecall?', [
            'lat' => $city_coordinates[0]['lat'],
            'lon' => $city_coordinates[0]['lon'],
            'exclude' => 'current,minutely,hourly,alert',
            'units' => 'metric',
            'lang' => 'ru',
            'appid' => getenv('WEATHER_TOKEN')
        ]), true);
        $weather = $util->transformWeather($daily_temp);
        $vk->sendMessage($peer_id, $weather);
        return;
    }
    if(mb_strtolower($message) === 'бот_вики_случайная') {
        $vk->sendMessage($peer_id, 'https://ru.wikipedia.org/wiki/%D0%A1%D0%BB%D1%83%D0%B6%D0%B5%D0%B1%D0%BD%D0%B0%D1%8F:%D0%A1%D0%BB%D1%83%D1%87%D0%B0%D0%B9%D0%BD%D0%B0%D1%8F_%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B8%D1%86%D0%B0');
        return;
    }
    if(preg_match('/(бот_вики_)[а-яёa-z]{2,}/u', mb_strtolower($message))) {
        $wiki_search_word = explode('_', mb_strtolower($message))[2];
        $wiki_search_word = str_replace("+", '_', $wiki_search_word);
        $wiki_search_word = str_replace(" ", '_', $wiki_search_word);
        $lang = explode('_', mb_strtolower($message))[3] ?? 'ru';
        $vk->sendMessage($peer_id, 'https://'. $lang .'.wikipedia.org/wiki/' . mb_strtolower($wiki_search_word));
        return;
    }
    if(preg_match('/(бот_гугл_)[а-яёa-z]{2,}/u', mb_strtolower($message))) {
        $google_search_word = explode('_', mb_strtolower($message))[2];
        $google_search_word = str_replace(" ", '+', $google_search_word);
        $vk->sendMessage($peer_id, 'https://www.google.ru/search?q=' . mb_strtolower($google_search_word));
        return;
    }
    if(preg_match('/(бот_скажи_)[а-яёa-z]{2,}/u', mb_strtolower($message))) {
        $speech = explode('_', mb_strtolower($message))[2];
        $file_created = $tts->createOpusFileFromText($speech);
        if($file_created) {

            $upload_link = json_decode($vk->getUploadLinkForAudioMessage('-212296161'), true);
            $vk->sendMessage($peer_id, print_r($upload_link));

            error_log(print_r($upload_link));

            $file_link = json_decode($util->curlPostRequest($upload_link['upload_url'], ['file' => realpath('tmp_file.opus')]), true);

            $saved_audio_file = json_decode($vk->saveAudioMessage($file_link['file']), true);

            $vk->sendMessageWithAudio($peer_id, 'doc' . $saved_audio_file['owner_id'] . '_' . $saved_audio_file['id']);
            $tts->deleteTmpFiles();
        } else {
            $vk->sendMessage($peer_id, 'Не скажу');
        }

        return;
    }
    if(preg_match('/(бот_посчитай_){1,20}/u', mb_strtolower($message))) {
        $expression = explode('_', mb_strtolower($message))[2];
        if(str_contains($expression, '%')) {
            $expression = str_replace("%", '%25', $expression);
        }
        if(str_contains($expression, '+')){
            $expression = str_replace("+", '%2B', $expression);
        }
        if(str_contains($expression, '^')){
            $expression = str_replace("^", '%5E', $expression);
        }
        if(str_contains($expression, '/')){
            $expression = str_replace("/", '%2F', $expression);
        }
        $responseHtml = $util->curlGetRequest('https://api.mathjs.org/v4/?expr=' . $expression);
        $vk->sendMessage($peer_id, 'Ответ -> ' . $responseHtml);
        return;
    }
    if(random_int(1,100) === 33) {
        $message = $util->transfromWordsHuebot($message);
        $vk->sendMessage($peer_id, $message);
        return;
    }
}

