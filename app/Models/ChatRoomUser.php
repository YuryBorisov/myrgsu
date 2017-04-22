<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomUser extends Model
{
    protected $fillable = ['user_id', 'room_id'];

    public $timestamps = false;
}
