<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AddressRepository extends BaseRepository
{
    protected $model = Address::class;

    protected $cacheName = 'address';

    protected function getCachedWhereNameAndRoom()
    {
        return Cache::tags([$this->cacheName, 'nameRoom']);
    }

    protected function getCachedWhereName()
    {
        return Cache::tags([$this->cacheName, 'name']);
    }

    public function getWhereNameAndRoom($name, $room)
    {
        if(!$this->getCachedWhereNameAndRoom()->has($str = $name . $room))
        {
            if($r = Address::whereName($name)->whereRoom($room)->first(['*']))
            {
                $r = $r->toArray();
                $this->getCachedWhereNameAndRoom()->forever($str, $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedWhereNameAndRoom()->get($str);
    }

    public function getWhereName($name)
    {
        if(!$this->getCachedWhereName()->has($name))
        {
            if($r = call_user_func([$this->model, 'whereName'], $name)->get())
            {
                $r = $r->toArray();
                return $r;
            }
            return false;
        }
        return $this->getCachedWhereName()->get($name);
    }

}