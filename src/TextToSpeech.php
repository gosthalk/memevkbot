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

    public function createOpusFileFromText($text): bool
    {
        $file_created = false;

        $file = 'tmp_file.wav';

        if (!file_exists($file))
        {

            $wav = $this->util->curlGetRequest('http://api.voicerss.org/?', [
                'key' => $this->api_key,
                'hl' => 'ru-ru',
                'src' => $text,
            ]);

            file_put_contents($file, $wav);

            exec('ffmpeg -i tmp_file.wav -ar 16000 -b:a 16k -c:a libopus tmp_file.ogg');

            $file_created = true;
        }

        return $file_created;
    }

    public function deleteTmpFiles()
    {
        unlink(realpath('tmp_file.wav'));
        unlink(realpath('tmp_file.opus'));
    }
}