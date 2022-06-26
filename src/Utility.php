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

    public function getAudioMessageUploadLink()
    {
        $url = 'https://api.vk.com/method/docs.getMessagesUploadServer?type=audio_message&peer_id=384485246&access_token='. getenv('ACCESS_TOKEN') .'&v=5.131';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function curlGetRequest($url, $params = []): bool|string
    {
        $link = $url . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cache-Control: no-cache']);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log((string)$httpCode);
        error_log($error ? '1' : '0');
        error_log(gettype($response));
        error_log($response ? '1' : '0');

        return $response;
    }

    public function curlPostRequest($url, $params = []): bool|string
    {
        $request = curl_init($url);

        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $params);
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($request);
        curl_close($request);

        return $response;
    }

    public function curlPostFileUpload($url, $file)
    {
            $headers = ["Content-Type:multipart/form-data"];
            $post_fields = [
                "file" => new \CurlFile($file, 'audio/opus', 'tmp_file.opus')
            ];
            $ch = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_RETURNTRANSFER => true
            ];
            curl_setopt_array($ch, $options);
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

    public function transfromWordsHuebot(string $message): string
    {
        $arr = explode(" ", $message);
        foreach ($arr as $index => $word) {
            if(mb_strlen($word) > 2){
                $arr[$index] = "хуе" . mb_strtolower($word);
            }
        }

        return implode(" ", $arr);
    }
}