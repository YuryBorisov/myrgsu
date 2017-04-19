<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepositories extends BaseRepository
{
    protected $model = News::class;

    protected $cacheName = 'news';

    protected $isAddName = false;

    public function getAll()
    {
        $id = 'all';
        if(!$this->isById($id))
        {
            if($r = News::orderBy('id', 'desc')->get())
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