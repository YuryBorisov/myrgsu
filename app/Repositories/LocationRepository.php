<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository extends BaseRepository
{
    protected $model = Location::class;

    protected $cacheName = 'location_user';

    public function all()
    {
        $id = 'all';
        if(!$this->isById($id))
        {
            if($r = Location::all())
            {
                $r = $r->toArray();
                $this->addById($id, $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedById()->get($id);
    }

}