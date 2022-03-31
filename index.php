<?php

use App\VkApiGateway;
use App\Utility;

require __DIR__ . '/vendor/autoload.php';

$conf = require("config/config.php");

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
    if($message === 'спиздани'){
        $vk->sendMessage($peer_id, "Я ебучий автобот");
    }
    if($message === 'стикер'){
        $stickerId = random_int(70913, 70960);
        $vk->sendMessageWithSticker($peer_id, $stickerId);
    }
    if($message === 'мем'){
        $owner_id = '-150550417';
        $posts = json_decode($vk->getGroupWallPosts($owner_id, 30), true);
        $ids = $util->getPostsIds($posts);
        $random_post_int = random_int(0, count($ids));
        $vk->sendMessage($peer_id, 'Держи', 'wall' . $owner_id . '_' . $ids[$random_post_int]);
    }
}

