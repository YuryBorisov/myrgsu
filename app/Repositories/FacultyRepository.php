<?php

namespace App\Repositories;

use App\Models\Faculty;
use App\Models\Group;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FacultyRepository extends BaseRepository
{
    protected $cacheName = 'faculty';

    protected $model = Faculty::class;

    protected $addName = 'short_name';

    public function getCachedAll()
    {
        return Cache::tags([$this->cacheName, 'all']);
    }

    public function getCachedGroups()
    {
        return Cache::tags([$this->cacheName, 'groups']);
    }

    public function all()
    {
        if(!$this->getCachedAll()->has('all'))
        {
            if($r = call_user_func([$this->model, 'all']))
            {
                $r = $r->toArray();
                $this->getCachedAll()->forever('all', $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedAll()->get('all');
    }

    public function getById($id)
    {
        if(!$this->isById($id))
        {
            if($r = Faculty::with('groups')->find($id))
            {
                $r = $r->toArray();
                $this->addById($id, $r);
                if($this->isAddName)
                {
                    $this->add($id, $r[$this->addName], $r);
                }
                return $r;
            }
            return false;
        }
        return $this->getCachedById()->get($id);
    }

}