<?php
declare(strict_types=1);

namespace App;

class VkApiGateway
{
    private string $VK_API_VERSION = '5.131';
    private string $VK_API_ENDPOINT = "https://api.vk.com/method/";

    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function _vkApi_call($method, $params = array()) {
        $params['access_token'] = $this->config['group_token'];
        $params['v'] = $this->VK_API_VERSION;
        $url = $this->VK_API_ENDPOINT.$method.'?'.http_build_query($params);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($json, true);
        var_dump($response);
        return $response['response'];
    }

    public function vkApi_messagesSend($peer_id, $message, $attachments = array()) {
        return $this->_vkApi_call('messages.send', array(
            'peer_id' => $peer_id,
            'message' => $message,
            'attachment' => implode(',', $attachments)
        ));
    }

    public function vkApi_getInviteLink($peer_id) {
        return $this->_vkApi_call('messages.getInviteLink', array(
            'peer_id' => $peer_id,
        ));
    }

    public function vkApi_getConversationMembers($peer_id) {
        return $this->_vkApi_call('messages.getConversationMembers', array(
            'peer_id' => $peer_id,
        ));
    }
}