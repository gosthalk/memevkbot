<?php
declare(strict_types=1);

namespace App;

class VkApiGateway
{
    function __construct($token, $version) {
        $this->token = getenv('ACCESS_TOKEN');
        $this->version = $version;
        $this->endpoint = "https://api.vk.com/method";
        $this->random_id = random_int(1, 9999999);
    }

    public function SendMessage($peer_id, $message, $attachment = null) {
        $this->Request("messages.send", array("peer_id" => $peer_id, "message" => $message, "attachment" => $attachment, "random_id" => $this->random_id));
    }

    private function Request($method, $params=array()) {
        $url = $this->endpoint."/$method?";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)."&access_token=".$this->token."&v=".$this->version);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cache-Control: no-cache'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        $data = curl_exec($ch);
        curl_close($ch);
    }

    public function sendOK(){
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