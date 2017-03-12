<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTelegram extends Model
{

    protected $fillable = ['id', 'faculty_id', 'group_id', 'call'];

}