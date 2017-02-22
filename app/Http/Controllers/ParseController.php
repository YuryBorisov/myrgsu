<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Repositories\AddressRepository;
use App\Repositories\DayRepository;
use App\Repositories\GroupRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\TimeRepository;
use App\Repositories\WeekRepository;
use Illuminate\Support\Facades\Log;
use Yangqi\Htmldom\Htmldom;

class ParseController extends Controller
{

    public function parse()
    {

        ini_set('memory_limit', '-1');

        $url = 'http://rgsu.net/for-students/timetable/timetable.html?template=&action=index&admin_mode=&f_Faculty=2&group=';

        $html = new \Yangqi\Htmldom\Htmldom($url);

        $groups = $html->find("div[class=small-12 medium-6 large-8 columns]")[0]->find("select[id=group]")[0]->find("option");

        $i22 = 0;

        $subject = [];

        foreach ($groups as $group)
        {
            $nameGroup = $group->innertext;
            $html = new Htmldom($url . $nameGroup);
            $nameGroup = ($groupRepository = GroupRepository::instance()->getByName($nameGroup)) ?
                $groupRepository['id'] : Group::create([
                    'short_name' => $nameGroup,
                    'faculty_id' => 2
                ])->id;
            foreach ($html->find("div[class=small-12 large-6 columns]") as $h)
            {
                $w = $i22 == 0 ? "Нечетная неделя" : "Четная неделя";
                $w = WeekRepository::instance()->getByName($w)['id'];
                $i22++;
                $days = $h->find("table[class=timetable]")[0]->find("[class=day]");
                for($iD = 0, $jD = count($days); $iD < $jD; $iD++)
                {
                    $trs = $days[$iD]->find('tr');
                    $dayId = 0;
                    for($i = 0, $j = count($trs); $i < $j; $i++)
                    {
                        if($i == 0)
                        {
                            $dayId = DayRepository::instance()->getByName($trs[$i]->find('[class=name]')[0]->innertext)['id'];
                            continue;
                        }
                        $tds = $trs[$i]->find('td');
                        for ($i2 = 0, $j2 = count($tds); $i2 < $j2; $i2++)
                        {
                            try
                            {
                                switch ($i2)
                                {
                                    case 0:
                                        $subject[$nameGroup][$w][$dayId][$i]['time_id'] = TimeRepository::instance()
                                            ->getByName($tds[$i2]->find('span[class=time-start]')[0]->innertext . '-'
                                                .$tds[$i2]->find('span[class=time-end]')[0]->innertext)['id'];
                                        break;
                                    case 1:
                                        $arr = explode('-', $tds[$i2]->innertext);
                                        if($arr[1] == 'Спортивный зал')
                                        {
                                            $arr[1] = 15937;
                                        }
                                        $subject[$nameGroup][$w][$dayId][$i]['address_id'] =
                                            ($addressRepository = AddressRepository::instance()->getWhereNameAndRoom($arr[0], $arr[1]))
                                            ? $addressRepository['id']
                                                :
                                                Address::create(['name' => $arr[0], 'room' => $arr[1]])->id;
                                        break;
                                    case 2:
                                        $subject[$nameGroup][$w][$dayId][$i]['subject'] = $tds[$i2]->innertext;
                                        break;
                                    case 3:
                                        $subject[$nameGroup][$w][$dayId][$i]['teacher_id'] =
                                            ($teacherRepository = TeacherRepository::instance()->
                                            getByName($teacherName = rtrim(explode("<", $tds[$i2]->innertext)[0]))) ?
                                                $teacherRepository['id'] :
                                                Teacher::create(['name' => $teacherName])->id;
                                        break;
                                }
                            }
                            catch (\Throwable $e)
                            {
                                Log::info($e->getMessage());
                                continue;
                            }

                        }
                    }
                }
            }
            $i22 = 0;
        }
        $insertSubject = [];
        for($sGroupKeys = array_keys($subject), $iGroup = 0, $jGroup = count($sGroupKeys); $iGroup < $jGroup; $iGroup++)
        {
            for($sWeekKeys = array_keys($subject[$sGroupKeys[$iGroup]]), $iWeek = 0, $jWeek = count($sWeekKeys); $iWeek < $jWeek; $iWeek++)
            {
                for($sDayKeys = array_keys($subject[$sGroupKeys[$iGroup]][$sWeekKeys[$iWeek]]), $iDay = 0, $jDay = count($sDayKeys); $iDay < $jDay; $iDay++)
                {
                    foreach ($subject[$sGroupKeys[$iGroup]][$sWeekKeys[$iWeek]][$sDayKeys[$iDay]] as $item)
                    {
                        $insertSubject[] = [
                            'name' => $item['subject'],
                            'week_id' => $sWeekKeys[$iWeek],
                            'day_id' => $sDayKeys[$iDay],
                            'group_id' => $sGroupKeys[$iGroup],
                            'time_id' => $item['time_id'],
                            'address_id' => $item['address_id'],
                            'teacher_id' => $item['teacher_id']
                        ];
                    }
                }
            }
            Subject::insert($insertSubject);
            $insertSubject = [];
        }
    }

}