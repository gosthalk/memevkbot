<?php


namespace App;


class TextToSpeech
{
    private $api_key;

    private Utility $util;

    public function __construct($api_key, $util)
    {
        $this->api_key = $api_key;
        $this->util = $util;
    }

    public function createOggFileFromText($text, $lang): bool
    {
        $file_created = false;

        $file = 'tmp_file.ogg';

        $lang_keys = require_once('src/text_context_keys.php');

        $lg = is_null($lang) ? ($lang_keys[$lang] ?? $lang_keys['ru']) : $lang_keys['ru'];
        error_log($lg);

        if (!file_exists($file))
        {

            if($lg === 'ru-ru') {
                $ogg = $this->util->curlGetRequest('http://api.voicerss.org/?', [
                    'key' => $this->api_key,
                    'c' => 'OGG',
                    'v' => 'Peter',
                    'hl' => $lg,
                    'src' => $text,
                ]);
            } else {
                $ogg = $this->util->curlGetRequest('http://api.voicerss.org/?', [
                    'key' => $this->api_key,
                    'c' => 'OGG',
                    'hl' => $lg,
                    'src' => $text,
                ]);
            }

            file_put_contents($file, $ogg);

            $file_created = true;
        }

        return $file_created;
    }

    public function deleteTmpFiles()
    {
        unlink(realpath('tmp_file.ogg'));
    }
}