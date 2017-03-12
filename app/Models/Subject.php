<?php

namespace App\Models;

use App\Repositories\WeekRepository;
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

    protected $hidden = ['day_id', 'time_id'];

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    public function day()
    {
        return  $this->hasOne(Day::class, 'id', 'day_id');
    }

    public function time()
    {
        return $this->hasOne(Time::class, 'id', 'time_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    public function scopeActiveWeek($query)
    {
        $query->where('week_id', WeekRepository::instance()->active()['id']);
    }

}
