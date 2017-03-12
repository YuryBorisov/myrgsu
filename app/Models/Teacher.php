<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{

    protected $table = 'teachers';

    public $timestamps = false;

    protected $fillable = ['name'];

}
