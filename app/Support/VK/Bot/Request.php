<?php

namespace App\Support\VK\Bot;

use App\Support\Base\BaseRequest;

class Request extends BaseRequest
{

    public static function sendMessage($params)
    {
        $params['access_token'] = env('VK_BOT_KEY');
        $params['v'] = '5.84';
        return self::curl('https://api.vk.com/method/messages.send', $params);
    }

    public static function getComments()
    {
        return self::curl('https://api.vk.com/method/board.getComments', [
            'access_token' => env('VK_TOKEN'),
            'group_id' => 144482898,
            'topic_id' => 35459441,
            'extended' => true,
            'count' => 100,
            'v' => '5.84'
        ]);
    }

    public static function sendAttachment($params)
    {
        return self::curl('https://api.vk.com/method/messages.send', $params);
    }

    public static function markAsRead($params)
    {
        return self::curl('https://api.vk.com/method/messages.send', $params);
    }

    public static function getUser($params)
    {
        return self::curl('https://api.vk.com/method/users.get', $params);
    }

}