<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    protected $table = 'weeks';

    public $timestamps = false;

    protected $fillable = ['name'];
}
