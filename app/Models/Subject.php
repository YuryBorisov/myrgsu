<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'week_id',
        'day_id',
        'group_id',
        'time_id',
        'address_id',
        'teacher_id'
    ];

}
