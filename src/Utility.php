<?php
declare(strict_types=1);

namespace App;

class Utility
{
    public function getPostsIds($posts): array
    {
        $ids = [];
        for($i=0;$i<count($posts['response']['items']);$i++){
            $ids[] = $posts['response']['items'][$i]['id'];
        }

        return $ids;
    }

    public function curlGetRequest($url, $params = []): bool|string
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

    public function transformNews($news): string
    {
        $str = 'Новости : ' . PHP_EOL;
        for($i=0;$i<10;$i++) {
            $str .= date('H:i d-m-Y', (int) $news['items'][$i]['timestamp']) . PHP_EOL;
            $str .= 'Новость -> ' . str_replace('&quot;','"',$news['items'][$i]['title']) . PHP_EOL;
            $str .= 'Ссылка -> ' . $news['items'][$i]['url'] . PHP_EOL;
            $str .= '-------------------------------------------------------------------------------------' . PHP_EOL;
        }
        return $str;
    }

    public function transformWeather($weather): string
    {
        $str = 'Погода : ' . PHP_EOL;

        for ($i=0;$i<count($weather['daily']);$i++) {
            $str .= "Дата " . date('d-m-Y', (int) $weather['daily'][$i]['dt']) . PHP_EOL;
            $str .= "Днем -> " . (int) $weather['daily'][$i]['temp']['day'] . '°C' . PHP_EOL;
            $str .= "Ночью -> " . (int) $weather['daily'][$i]['temp']['night'] . '°C' . PHP_EOL;
            $str .= "Влажность -> " . $weather['daily'][$i]['humidity'] . '%' . PHP_EOL;
            $str .= "Скорость ветра -> " . round($weather['daily'][$i]['wind_speed'], 1) . ' м/с' .PHP_EOL;
            $str .= "Текстовая погода -> " . $weather['daily'][$i]['weather'][0]['description'] . PHP_EOL;
            $str .= '-------------------------------------------------------------------------------------' . PHP_EOL;
        }

        return $str;
    }

    public function strContains(string $str, array $arr): bool
    {
        foreach($arr as $a) {
            if (stripos($str,$a) !== false) return true;
        }
        return false;
    }
}