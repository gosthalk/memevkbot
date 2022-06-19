<?php

use App\VkApiGateway;
use App\Utility;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

//$conf = require('config/config.php');
//$vk = new VkApiGateway($conf['user_token'], $conf['group_token'], $conf['weather_token'], '5.131');

$vk = new VkApiGateway(getenv('USER_TOKEN'), getenv('ACCESS_TOKEN'), '5.131');
$util = new Utility();
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
    if(preg_match('/(бот_новости_)[а-яё]{2,}/', mb_strtolower($message))) {
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
    if(preg_match('/(бот_погода_)[а-яё]{2,}/', mb_strtolower($message))) {
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
    if(preg_match('/(бот_вики_)[а-яёa-z]{2,}/', mb_strtolower($message))) {
        $wiki_search_word = explode('_', mb_strtolower($message))[2];
        $wiki_search_word = str_replace("+", '_', $wiki_search_word);
        $lang = explode('_', mb_strtolower($message))[3] ?? 'ru';
        $vk->sendMessage($peer_id, 'https://'. $lang .'.wikipedia.org/wiki/' . mb_strtolower($wiki_search_word));
        return;
    }
    if(preg_match('/(бот_гугл_)[а-яёa-z]{2,}/', mb_strtolower($message))) {
        $wiki_search_word = explode('_', mb_strtolower($message))[2];
        $vk->sendMessage($peer_id, 'https://www.google.ru/search?q=' . mb_strtolower($wiki_search_word));
        return;
    }
    if(random_int(1,150) === 33) {
        $message = $util->transfromWordsHuebot($message);
        $vk->sendMessage($peer_id, $message);
        return;
    }
}

