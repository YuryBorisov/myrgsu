<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AddressRepository extends BaseRepository
{
    protected $model = Address::class;

    protected $cacheName = 'address';

    protected function getCachedWhereName()
    {
        return Cache::tags([$this->cacheName, 'name']);
    }

    public function getWhereName($name)
    {
        if(!$this->getCachedWhereName()->has($name))
        {
            if($r = call_user_func([$this->model, 'whereName'], $name)->first())
            {
                $r = $r->toArray();
                $this->getCachedWhereName()->forever($name, $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedWhereName()->get($name);
    }

}