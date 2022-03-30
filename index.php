<?php

use App\VkApiGateway;

require __DIR__ . '/vendor/autoload.php';

$vk = new VkApiGateway(getenv('ACCESS_TOKEN'), '5.131');
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
}

