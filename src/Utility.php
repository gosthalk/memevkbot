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
        $ch = curl_init($url . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function transformNews($news)
    {
        $str = 'Новости : ' . PHP_EOL;
        foreach ($news as $n) {
            $str .= date('H:i d-m-Y', $n['timestamp']) . ' - ' . $n['title'] . PHP_EOL;
        }

        return $str;
    }
}