<?php

namespace App\Repositories;

use App\Models\Day;

class DayRepository extends BaseRepository
{
    protected $model = Day::class;

    protected $cacheName = 'day';
}