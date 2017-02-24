<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\Subject;

class GroupRepository extends BaseRepository
{

    protected $model = Group::class;

    protected $cacheName = 'groups';

    protected $addName = 'short_name';

    protected $getMethodName = 'whereShortName';

    protected $prefixGetSubjectDay = 'day';

    public function getActiveSubjectDay($groupId, $dayId)
    {
        if(!$this->isById($key = $groupId . $this->prefixGetSubjectDay . $dayId))
        {
            if($r = Subject::with('time', 'address', 'teacher')->whereGroupIdAndDayId($groupId, $dayId)->activeWeek()->get())
            {
                $r = [
                    'subjects' => $r->toArray()
                ];
                $r['week'] = WeekRepository::instance()->active();
                $r['day'] = DayRepository::instance()->get($dayId);
                $this->addById($key, $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedById()->get($key);
    }

}