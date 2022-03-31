<?php

use App\VkApiGateway;
use App\Utility;

require __DIR__ . '/vendor/autoload.php';

//$conf = require('config/config.php');
//$vk = new VkApiGateway($conf['user_token'], $conf['group_token'], '5.131');

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
        $vk->sendMessage($peer_id, "Я ебучий автобот");
    }
    if(mb_strtolower($message) === 'стикер') {
        $stickersArray = require_once('src/stickers.php');
        $stickerPackId = random_int(1,6);
        $stickerId = random_int($stickersArray[$stickerPackId][0],$stickersArray[$stickerPackId][1]);
        $vk->sendMessageWithSticker($peer_id, $stickerId);
    }
    if(mb_strtolower($message) === 'мем') {
        $owner_id = '-150550417';
        $posts = json_decode($vk->getGroupWallPosts($owner_id, 30), true);
        $ids = $util->getPostsIds($posts);
        $random_post_int = random_int(0, count($ids));
        $vk->sendMessage($peer_id, 'Держи', 'wall' . $owner_id . '_' . $ids[$random_post_int]);
    }
    if(preg_match('/(бот_новости_)[а-яё]{2,}/', mb_strtolower($message))) {
        $news_word = explode('_', mb_strtolower($message))[2];
        $news = json_decode($util->curlGetRequest('https://mediametrics.ru/satellites/api/search/?',
        [
            'ac' => 'search',
            'q' => $news_word,
            'p' => 0,
            'c' => 'ru',
            'callback' => 'JSON'
        ]), true);
        $newsString = $util->transformNews($news);
        $vk->sendMessage($peer_id, $newsString);
    }
}

