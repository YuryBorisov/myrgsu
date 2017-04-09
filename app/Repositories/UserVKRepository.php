<?php

namespace App\Repositories;

use App\Models\UserVK;
use Illuminate\Support\Facades\Log;

class UserVKRepository extends BaseRepository
{

    protected $model = UserVK::class;

    protected $cacheName = 'user_vk';

    protected $isAddName = false;

    public function addValue($userId, $key, $value)
    {
        $user = $this->get($userId);
        $user[$key] = $value;
        $this->clear($userId);
        $this->getCachedById()->forever($userId, $user);
    }

    public function addCommandEnd($userId, $command)
    {
        if(!($user = $this->get($userId)))
        {
            return false;
        }
        $user['commands']['end'] = $command;
        $this->addById($userId, $user);
    }

    public function getCommandEnd($userId)
    {
        if(!($user = $this->get($userId)))
        {
            return false;
        }
        return isset($user['commands']['end']) ? $user['commands']['end'] : false;
    }

    public function getValue($userId, $key)
    {
        if(!($user = $this->get($userId)))
        {
            return false;
        }
        return isset($user[$key]) ? $user[$key] : false;
    }

    public function removeValue($userId, $key)
    {
        $user = $this->get($userId);
        unset($user[$key]);
        $this->clear($userId);
        $this->getCachedById()->forever($userId, $user);
    }

}