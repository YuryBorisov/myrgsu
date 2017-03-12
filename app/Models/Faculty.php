<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    public $timestamps = false;

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

}
