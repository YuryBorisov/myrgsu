<?php
namespace App\Support\Base;


abstract class BaseRequest
{

    protected static function curl($url, $params)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $out = curl_exec($curl);
        curl_close($curl);
        return $out;
    }

    public abstract static function sendMessage($params);

    public abstract static function sendAttachment($params);

    public abstract static function getUser($params);

}