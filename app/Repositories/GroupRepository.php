<?php

namespace App\Repositories;


use App\Models\Group;

class GroupRepository extends BaseRepository
{

    protected $model = Group::class;

    protected $cacheName = 'groups';

    protected $addName = 'short_name';

    protected $getMethodName = 'whereShortName';

}