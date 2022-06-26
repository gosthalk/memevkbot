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

    public function createOggFileFromText($text): bool
    {
        $file_created = false;

        $file = 'tmp_file.ogg';

        if (!file_exists($file))
        {

            $ogg = $this->util->curlGetRequest('http://api.voicerss.org/?', [
                'key' => $this->api_key,
                'c' => 'OGG',
                'v' => 'Peter',
                'hl' => 'ru-ru',
                'src' => $text,
            ]);

            file_put_contents($file, $ogg);

            //exec('ffmpeg -i tmp_file.wav -ar 16000 -b:a 16k -c:a libopus tmp_file.opus');

            $file_created = true;
        }

        return $file_created;
    }

    public function deleteTmpFiles()
    {
        unlink(realpath('tmp_file.ogg'));
    }
}