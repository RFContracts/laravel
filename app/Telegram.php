<?php

namespace App;



use Illuminate\Support\Facades\URL;

class Telegram
{
    /**
     * @param $filename
     */
    public function setMessage($filename)
    {
        $bot_api_key = config('config.bot_key');
        $api_url = config('config.api_url');
        $chat_id = config('config.chat');
        $text = URL::to('/storage/'. $filename);
        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL,$api_url.$bot_api_key.'/sendMessage');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            'chat_id='.$chat_id.'&text='.urlencode($text));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $result=curl_exec($curl);
        curl_close($curl);
    }
}
