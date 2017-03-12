<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    public $timestamps = false;

    protected $fillable = ['short_name', 'full_name', 'faculty_id'];



}