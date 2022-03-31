<?php
declare(strict_types=1);

namespace App;

class Utility
{
    public function getPostsIds($posts)
    {
        $ids = [];
        for($i=0;$i<count($posts['response']['items']);$i++){
            $ids[] = $posts['response']['items'][$i]['id'];
        }

        return $ids;
    }

    public function curlGetRequest($url, $params = [])
    {
        $link = $url . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cache-Control: no-cache']);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function transformNews($news)
    {
        $str = 'Новости : ' . PHP_EOL;
        foreach ($news['items'] as $n) {
            $str .= date('H:i d-m-Y', (int) $n['timestamp']) . ' - ' . str_replace('&quot;','\"',$n['title']) . PHP_EOL;
            $str .= '---------------------------------------------------------------------------' . PHP_EOL;
        }

        return $str;
    }
}