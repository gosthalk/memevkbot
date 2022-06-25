<?php
declare(strict_types=1);

namespace App;

class VkApiGateway
{
    private $group_token;
    private $user_token;
    private $version;
    private $endpoint;
    private $random_id;

    public function __construct($user_token, $group_token, $version)
    {
        $this->user_token = $user_token;
        $this->group_token = $group_token;
        $this->version = $version;
        $this->endpoint = "https://api.vk.com/method";
        $this->random_id = random_int(1, 9999999);
    }

    public function SendMessage($peer_id, $message, $attachment = null)
    {
        $this->Request("messages.send", $this->group_token, ["peer_id" => $peer_id, "message" => $message, "attachment" => $attachment, "random_id" => $this->random_id]);
    }

    public function SendMessageWithSticker($peer_id, $stickerId)
    {
        $this->Request("messages.send", $this->group_token, ["peer_id" => $peer_id, "sticker_id" => $stickerId, "random_id" => $this->random_id]);
    }

    public function SendMessageWithAudio($peer_id, $attachment)
    {
        $this->Request("messages.send", $this->group_token, ["peer_id" => $peer_id, "attachment" => $attachment]);
    }

    public function getGroupWallPosts($owner_id, $count)
    {
        return $this->Request("wall.get", $this->user_token, ["owner_id" => $owner_id, "count" => $count]);
    }

    public function getUploadLinkForAudioMessage($peer_id)
    {
        return $this->Request("docs.getMessagesUploadServer", $this->group_token/*, ["type" => 'audio_message', "peer_id" => $peer_id]*/);
    }

    public function saveAudioMessage($file_link)
    {
        return $this->Request("docs.save", $this->group_token, ["file" => $file_link]);
    }

    private function Request($method, $token, $params=[])
    {
        $url = $this->endpoint."/$method?";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)."&access_token=".$token."&v=".$this->version);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cache-Control: no-cache']);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log((string)$httpCode);
        error_log($error ? '1' : '0');
        error_log($data ? '1' : '0');

        return $data;
    }

    public function sendOK()
    {
        echo 'ok';
        $response_length = ob_get_length();
        if (is_callable('fastcgi_finish_request')) {
            session_write_close();
            fastcgi_finish_request();
            return;
        }

        ignore_user_abort(true);

        ob_start();
        $serverProtocole = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocole.' 200 OK');
        header('Content-Encoding: none');
        header('Content-Length: '. $response_length);
        header('Connection: close');

        ob_end_flush();
        ob_flush();
        flush();
    }
}