<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //'name', 'email', 'password',
        'service_id',
        'user_id',
        'first_name',
        'last_name',
        'created_at'
    ];

    const NOTIFICATIONS_YES = 0;

    const NOTIFICATIONS_NO = 1;

    const NOTIFICATIONS = 'notifications';

    const NOTIFICATIONS_SCHEDULE = 'schedule';

    const NOTIFICATIONS_BROADCASTING = 'broadcasting';

    const NOTIFICATIONS_NEWS_PROJECT = 'news_project';

    const NOTIFICATIONS_NEWS_PROJECT_ID = 1;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];

    public $timestamps = false;

}
