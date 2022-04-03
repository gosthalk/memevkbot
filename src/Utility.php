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
        for($i=0;$i<10;$i++) {
            $str .= date('H:i d-m-Y', (int) $news['items'][$i]['timestamp']) . PHP_EOL;
            $str .= 'Новость - ' . str_replace('&quot;','"',$news['items'][$i]['title']) . PHP_EOL;
            $str .= 'Ссылка - ' . $news['items'][$i]['url'] . PHP_EOL;
            $str .= '-------------------------------------------------------------------------------------' . PHP_EOL;
        }
        return $str;
    }

    public function transformWeather($weather)
    {
        $str = 'Погода : ' . PHP_EOL;

        for ($i=0;$i<count($weather['daily']);$i++) {
            $str .= "Дата " . date('H:i d-m-Y', (int) $weather['daily'][$i]['dt']) . PHP_EOL;
            $str .= "Днем - " . $weather['daily'][$i]['temp']['day'] . PHP_EOL;
            $str .= "Ночью - " . $weather['daily'][$i]['temp']['night'] . PHP_EOL;
            $str .= "Влажность - " . $weather['daily'][$i]['humidity'] . PHP_EOL;
            $str .= "Скорость ветра - " . $weather['daily'][$i]['wind_speed'] .PHP_EOL;
            $str .= "Текстовая погода - " . $weather['daily'][$i]['weather'][0]['description'] . ' (пиздеж)' . PHP_EOL;
            $str .= '-------------------------------------------------------------------------------------' . PHP_EOL;
        }

        return $str;
    }
}