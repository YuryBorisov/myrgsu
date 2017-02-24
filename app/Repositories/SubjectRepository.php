<?php

namespace App\Repositories;

use App\Models\Subject;

class SubjectRepository extends BaseRepository
{

    protected $model = Subject::class;

    public function getById($id)
    {
        if(!$this->isById($id))
        {
            if($r = Subject::with('day', 'time')->find($id))
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