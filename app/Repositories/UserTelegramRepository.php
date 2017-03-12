<?php

namespace App\Repositories;


use App\Models\UserTelegram;

class UserTelegramRepository extends BaseRepository
{

    protected $model = UserTelegram::class;

    protected $cacheName = 'user_telegram';

    protected $isAddName = false;

    public function addValue($userId, $key, $value)
    {
        $user = $this->get($userId);
        $user[$key] = $value;
        $this->clear($userId);
        $this->getCachedById()->forever($userId, $user);
    }

    public function getValue($userId, $key)
    {
        $user = $this->get($userId);
        if(isset($user[$key]))
        {
            return $user[$key];
        }
        return false;
    }

    public function removeValue($userId, $key)
    {
        $user = $this->get($userId);
        unset($user[$key]);
        $this->clear($userId);
        $this->getCachedById()->forever($userId, $user);
    }

}