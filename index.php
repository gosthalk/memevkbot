<?php

use App\VkApiGateway;

require __DIR__ . '/vendor/autoload.php';

//$vkApi = new VkApiGateway($config);

//$vkApi->vkApi_messagesSend(2000000023, 'Ну привет епта');
//$vkApi->vkApi_getInviteLink(2000000023);
//$vkApi->vkApi_getConversationMembers(2000000023);


define('CALLBACK_API_CONFIRMATION_TOKEN', (string) getenv('CONFIRMATION_TOKEN')); // Строка, которую должен вернуть сервер
define('VK_API_ACCESS_TOKEN', (string) getenv('ACCESS_TOKEN')); // Ключ доступа сообщества

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation'); // Тип события о подтверждении сервера
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new'); // Тип события о новом сообщении
define('VK_API_ENDPOINT', 'https://api.vk.com/method/'); // Адрес обращения к API
define('VK_API_VERSION', '5.131'); // Используемая версия API

$event = json_decode(file_get_contents('php://input'), true);

switch ($event['type']) {
    // Подтверждение сервера
    case CALLBACK_API_EVENT_CONFIRMATION:
        echo(CALLBACK_API_CONFIRMATION_TOKEN);
        break;
    // Получение нового сообщения
    case CALLBACK_API_EVENT_MESSAGE_NEW:
        $message = $event['object']['message'];
        $peer_id = $message['peer_id'];
        if($message['text'] === 'спиздани'){
            send_message($peer_id, '@id'. $message['from_id'] . ' (Лошок) полный');
            echo('ok');
        }
        echo('ok');
        break;
    default:
        echo('Unsupported event');
        break;
}

function send_message($peer_id, $message)
{
    api('messages.send', array(
        'peer_id' => $peer_id,
        'message' => $message,
        'random_id' => random_int(1, 9999999)
    ));
}

function api($method, $params)
{
    $url = VK_API_ENDPOINT."/$method?";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)."&access_token=".VK_API_ACCESS_TOKEN."&v=".VK_API_VERSION);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cache-Control: no-cache'));
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    $data = curl_exec($ch);
    var_dump($data);
    curl_close($ch);
}

