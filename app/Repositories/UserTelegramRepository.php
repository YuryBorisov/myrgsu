<?php

namespace App\Repositories;


use App\Models\UserTelegram;

class UserTelegramRepository extends BaseRepository
{

    protected $model = UserTelegram::class;

    protected $cacheName = 'user_telegram';

    protected $isAddName = false;

}