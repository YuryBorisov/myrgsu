<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository
{

    protected $cacheName = 'users';

    protected $isAddName = false;

    public function get($userId, $serviceId = 0)
    {
        if(!$this->isById($userId, $serviceId))
        {
            if($user = User::where([['user_id', $userId], ['service_id', $serviceId]])->first())
            {
                $user = $user->toArray();
                $this->addById($userId.'_'.$serviceId, $user);
                return $user;
            }
            return false;
        }
        return $this->getCachedById()->get($userId.'_'.$serviceId);
    }

    protected function isById($userId, $serviceId = 0)
    {
        return $this->getCachedById()->has($userId.'_'.$serviceId);
    }

    public function addCommand($userId, $serviceId, $command)
    {
        $user = $this->get($userId, $serviceId);
        $user['commands'][] = $command;
        $user['commands']['end'] = $command;
        $this->addById($userId.'_'.$serviceId, $user);
        return $this->get($user['user_id'], $serviceId);
    }

    public function addMessageForBroadcasting($userId, $serviceId, $message)
    {
        $user = $this->get($userId, $serviceId);
        $user['broadcasting'] = $message;
        $this->addById($userId.'_'.$serviceId, $user);
        return $this->get($user['user_id'], $serviceId);
    }

    public function getCommandEnd($userId, $serviceId)
    {
        $user = $this->get($userId, $serviceId);
        return isset($user['commands']) ? $user['commands']['end'] : false;
    }

}