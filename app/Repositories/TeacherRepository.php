<?php

namespace App\Repositories;

use App\Models\Teacher;

class TeacherRepository extends BaseRepository
{
    protected $model = Teacher::class;

    protected $cacheName = 'teacher';

}