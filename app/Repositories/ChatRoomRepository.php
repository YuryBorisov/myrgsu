<?php

namespace App\Repositories;


use App\Models\ChatRoom;

class ChatRoomRepository extends BaseRepository
{
    protected $model = ChatRoom::class;

    protected $cacheName = 'chat_rooms';

    protected $isAddName = false;

    public function getAll()
    {
        $id = 'all';
        if(!$this->isById($id))
        {
            if($r = ChatRoom::all())
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