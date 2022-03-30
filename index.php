<?php

use App\VkApiGateway;

require __DIR__ . '/vendor/autoload.php';

$config = require('config/config.php');

$vkApi = new VkApiGateway($config);

//$vkApi->vkApi_messagesSend(2000000023, 'Ну привет епта');
//$vkApi->vkApi_getInviteLink(2000000023);
//$vkApi->vkApi_getConversationMembers(2000000023);


define('CALLBACK_API_CONFIRMATION_TOKEN', $config['user_app_id']); // Строка, которую должен вернуть сервер
define('VK_API_ACCESS_TOKEN', $config['group_token']); // Ключ доступа сообщества

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
        $message = $event['object'];
        $peer_id = $message['peer_id'] ?: $message['user_id'];
        send_message($peer_id, "Нихуя не понял");
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
    ));
}

function api($method, $params)
{
    $params['access_token'] = VK_API_ACCESS_TOKEN;
    $params['v'] = VK_API_VERSION;
    $query = http_build_query($params);
    $url = VK_API_ENDPOINT . $method . '?' . $query;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($curl);
    $error = curl_error($curl);
    if ($error) {
        error_log($error);
        throw new Exception("Failed {$method} request");
    }
    curl_close($curl);
    $response = json_decode($json, true);
    if (!$response || !isset($response['response'])) {
        error_log($json);
        throw new Exception("Invalid response for {$method} request");
    }
    return $response['response'];
}

print_r('Успешно');
