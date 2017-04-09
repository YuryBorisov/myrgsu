<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVK extends Model
{
    protected $table = 'users_vk';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'created_at'
    ];

    public $timestamps = false;

}