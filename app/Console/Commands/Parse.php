<?php

namespace App\Console\Commands;
use App\Models\Address;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Repositories\AddressRepository;
use App\Repositories\DayRepository;
use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\TimeRepository;
use App\Repositories\WeekRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yangqi\Htmldom\Htmldom;

class Parse extends Command
{

    protected $signature = 'parse';

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        exec("service memcached restart");

        //var_dump($c);

        Group::truncate();
        Subject::truncate();
        Teacher::truncate();
        Address::truncate();

        // key = DB, value = http://rgsu.net
        $arr = [
            1 => 1,
            3 => 5,
            4 => 7,
            5 => 8
        ];

        foreach ($arr as $key => $value) {
            $url = 'http://rgsu.net/for-students/timetable/timetable.html?template=&action=index&admin_mode=&f_Faculty='.$value.'&group=';

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
                        'faculty_id' => $key
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
                                                ->getByName(trim($tds[$i2]->find('span[class=time-start]')[0]->innertext . '-'
                                                    .$tds[$i2]->find('span[class=time-end]')[0]->innertext))['id'];
                                            break;
                                        case 1:
                                            /*
                                            $arr = explode('-', $tds[$i2]->innertext);
                                            if($arr[1] == 'Спортивный зал')
                                            {
                                                $arr[1] = 15937;
                                            } else if($arr[1] == 'актовый зал' || $arr[1] == 'Актовый зал')
                                            {
                                                $arr[1] = 15370;
                                            } else if($arr[1] = 'ВП4/12 СС БАССЕЙН') {
                                                $arr[1] = 15214;
                                            }
                                            */
                                            try {
                                                $adr = trim($tds[$i2]->innertext);
                                                $subject[$nameGroup][$w][$dayId][$i]['address_id'] =
                                                    ($addressRepository = AddressRepository::instance()->getWhereName($adr))
                                                        ? $addressRepository['id']
                                                        :
                                                        Address::create(['name' => $adr])->id;
                                            } catch (\Throwable $exception) {
                                                Log::info($exception->getMessage());
                                                return;
                                            }

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
                                'time_id' => isset($item['time_id']) ?  $item['time_id'] : null,
                                'address_id' => $item['address_id'],
                                'teacher_id' => $item['teacher_id']
                            ];
                        }
                    }
                }
                Subject::insert($insertSubject);
                $insertSubject = [];
            }
            $output->writeln([
                'Добавили ' . FacultyRepository::instance()->get($key)['full_name']
            ]);
            usleep(1000);
        }

        //return parent::execute($input, $output); // TODO: Change the autogenerated stub
    }

}