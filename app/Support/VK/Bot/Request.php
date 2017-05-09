<?php

namespace App\Support\VK\Bot;

use App\Support\Base\BaseRequest;

class Request implements BaseRequest
{
    public static function sendMessage($params)
    {
        $params['access_token'] = env('VK_BOT_KEY');
        $params['v'] = '5.0';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.vk.com/method/messages.send');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_exec($curl);
        curl_close($curl);
    }
}