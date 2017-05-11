<?php

namespace App\Repositories;

class BroadcastingRepository extends BaseRepository
{

    protected $cacheName = 'broadcasting';

    const TABLE_LOCATION = 1;

    const TABLE_FACULTY = 2;

    public function all()
    {
        if (!$this->isByName('all'))
        {
            $i = 1;
            $all = [];
            foreach (LocationRepository::instance()->all() as $l)
            {
                $all[] = [
                    'id' => $i,
                    'table' => self::TABLE_LOCATION,
                    'table_id' => $l['id'],
                    'name' => $l['name']
                ];
                $i++;
            }
            foreach (FacultyRepository::instance()->all() as $f)
            {
                $all[] = [
                    'id' => $i,
                    'table' => self::TABLE_FACULTY,
                    'table_id' => $f['id'],
                    'name' => $f['short_name']
                ];
                $i++;
            }
            $this->addByName('all', $all);
        }
        return $this->getCachedByName()->get('all');
    }

    public function getById($id, $serviceId = 0)
    {
        $b = false;
        foreach ($this->all() as $bb)
        {
            if($bb['id'] == $id)
            {
                $b = $bb;
                break;
            }
        }
        return $b;
    }

}