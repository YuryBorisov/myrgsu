<?php

namespace App\Repositories;

use App\Models\Time;

class TimeRepository extends BaseRepository
{
    protected $model = Time::class;

    protected $cacheName = 'time';
}