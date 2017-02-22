<?php

namespace App\Repositories;

use App\Models\Week;
use Illuminate\Support\Facades\Cache;

class WeekRepository extends BaseRepository
{

    protected $model = Week::class;

    protected $cacheName = 'week';

    public function all()
    {
        if(!$this->isByName('all'))
        {
            if($r = Week::all())
            {
                $r = $r->toArray();
                $this->addByName('all', $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedByName()->get('all');
    }

    public function active()
    {
        $weeks = $this->all();
        $active = null;
        foreach ($weeks as $week)
        {
            if($week['active'])
            {
                $active = $week;
                break;
            }
        }
        /*
         * if(date("w", mktime(0, 0, 0, date("m"), date("d"), date("Y"))) == 1)
        {
            Week::find($active['id'])->update(['active' => false]);
            Week::find($active['id'] == 1 ? 2 : 1)->update(['active' => true]);
            $this->clear(0, 'all');
            $active = $this->active();
        }
         */

        return $active;
    }

}