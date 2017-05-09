<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Address;
use App\Models\Service;
use App\Models\Subject;
use App\Repositories\GroupRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\TimeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WeekRepository;
use App\Support\VK\Bot\Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MainMenuCommand extends BaseVKCommand
{

    protected $commands = [
        0,
        self::PERSONAL_AREA_VIEW,
        self::MAIN_MENU_ROOM_VIEW
    ];

    public function view($title = true)
    {
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_SELECT);
        $distribution = $this->user['distribution'] == 0 ? 'ВКЛ' : 'ВЫКЛ';
        if($title)
        {
            $this->text .= "\xF0\x9F\x8C\x80 Главное меню \n".self::SEPARATOR."\n";
        }
        $this->text .= "1. \xF0\x9F\x8E\x92 Личный кабинет\n".
                       "2. \xF0\x9F\x9A\xAA Аудитории\n".
                       "3. \xF0\x9F\x91\x94 Преподаватели\n".
                       "4. \xF0\x9F\x93\xB0 Новости РГСУ\n".
                       "5. \xF0\x9F\x8E\xA7 Плеер\n".
                       "6. \xF0\x9F\x92\xAC Чат\n".
                       "7. \xF0\x9F\x93\xA2 Рассылка [{$distribution}]\n".
                       "8. \xE2\x9A\xA1 Пожелания/Улучшения";
        return $this->text;
    }

    public function mainSelect()
    {
        if(isset($this->commands[$this->message]))
        {
            $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, $this->commands[$this->message]);
            $this->text = (new Manager($this->user, $this->data))->run();
        }
        else
        {
            $this->text = self::NOT_COMMAND.$this->view(false);
        }
        return $this->text;
    }

    public function roomView()
    {
        $this->text = "Пришли мне аудиторию [Например: ВП8-408] и я скажу тебе есть ли в ней кто - нибудь сегодня.\n".
            "Для отмены выбора отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_ROOM_SELECT);
        return $this->text;
    }

    private function searchRoomSubject($subjects, $timeId)
    {
        foreach ($subjects as $s)
        {
            if($s['time_id'] == $timeId)
            {
                for ($h = 0; $h < $timeId; $h++) {
                    $this->text .= "\xE2\x8F\xB0 ";
                }
                $time = TimeRepository::instance()->get($timeId)['name'];
                return "(".$time.")\n".
                              "Группа: ".GroupRepository::instance()->get($s['group_id'])['short_name']."\n".
                              "Дисциплина: ".$s['name']."\n".
                              "Преподаватель: ".TeacherRepository::instance()->get($s['teacher_id'])['name']."\n\n";
            }
        }
    }

    public function selectRoom()
    {
        if(self::CANCELED != $this->message)
        {
            $arr = explode('-', $this->message);
            if(isset($arr[0]) && isset($arr[1]))
            {
                $address = Address::where('name', 'LIKE', '%'.$this->message.'%')->get();
                if($address->count() != 0)
                {
                    $ids = [];
                    foreach ($address as $a)
                    {
                        $ids[] = $a['id'];
                    }
                    $weekId = WeekRepository::instance()->active()['id'];
                    $subject = Subject::whereIn('address_id', $ids)->whereWeekId($weekId)->whereDayId(Carbon::now()->dayOfWeek)->get();
                    $this->text .= "Сегодня: ".date('d-m-Y')."\nАудитория: ".$this->message."\n\n";
                    for($i = 1; $i <= 10; $i++)
                    {
                        $this->text .= $this->searchRoomSubject($subject, $i);
                    }
                    $this->view(false);
                }
                else
                {
                    $this->text = "Я не смог найти эту аудиторию.\nПришлите ещё раз или отправьте цифру 100 для выхода.";
                }
            }
            else
            {
               $this->text = "Не верный формат.\nПример правильного формата: ВП8-408\nПришлите еще раз или отправьте цифру 100 для выхода.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

}