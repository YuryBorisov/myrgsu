<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Service;
use App\Models\User;
use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Repositories\WeekRepository;
use App\Support\VK\Bot\Manager;
use Carbon\Carbon;

class ScheduleCommand extends BaseVKCommand
{

    protected $commands = [
        self::MAIN_MENU_VIEW,
        self::PERSONAL_AREA_VIEW,
        self::SCHEDULE_FACULTY_VIEW,
        self::SCHEDULE_GROUP_VIEW,
        self::SCHEDULE_TODAY,
        self::SCHEDULE_TOMORROW_DAY,
        self::SCHEDULE_WEEK,
        self::SCHEDULE_CALL_VIEW,
        self::CANCELED => self::SCHEDULE_VIEW,
        1000 => self::SCHEDULE_VIEW
    ];

    public function view($title = true)
    {
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::SCHEDULE_SELECT);
        if($title)
        {
           $this->text .=  "\xF0\x9F\x93\x85 Моё расписание\n".self::SEPARATOR."\n";
        }
        $this->text .= "1. \xF0\x9F\x94\x99 Назад\n2. \xF0\x9F\x92\xBC Факультет: ";
        if($this->user['faculty_id'] != 0)
        {
            $faculty = FacultyRepository::instance()->get($this->user['faculty_id']);
            $this->text .= $faculty['short_name']."\n3. \xF0\x9F\x8E\x93 Группа: ";
            if($this->user['group_id'] != 0)
            {
                foreach ($faculty['groups'] as $group)
                {
                    if($group['id'] == $this->user['group_id'])
                    {
                        $callText = $this->user['call'] == 0 ? 'ВКЛ' : 'ВЫКЛ';
                        $this->text .= "{$group['short_name']}\n".
                                       "4. \xF0\x9F\x93\x84 Сегодняшние занятия\n".
                                       "5. \xE2\x8F\xA9 Завтрашнии занятия\n".
                                       "6. \xF0\x9F\x93\x91 Показать за неделю\n".
                                       "7. \xF0\x9F\x94\x8A Уведомления [{$callText}]\n";
                        break;
                    }
                }
            }
            else
            {
                $this->text .= "Не выбрана\n";
            }
        }
        else
        {
            $this->text .= "Не выбран\n";
        }
        $this->text .= "0. \xF0\x9F\x8C\x80 Главное меню";
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

    public function facultyView()
    {
        $faculties = FacultyRepository::instance()->all();
        $this->text =  "Список факультетов:\n";
        foreach ($faculties as $faculty) {
            $this->text .= "{$faculty['id']}. {$faculty['short_name']} - {$faculty['full_name']}\n";
        }
        $this->text .= "\nПришлите сокращённое название факультета или цифру.\nДля отмены выбора отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::SCHEDULE_FACULTY_SELECT);
        return $this->text;
    }

    public function selectFaculty()
    {
        if($this->message != 100)
        {
            if($faculty = (is_numeric($this->message) ? FacultyRepository::instance()->get($this->message) : FacultyRepository::instance()->getByName($this->message)))
            {
                if(User::where([['user_id', $this->user['user_id']], ['service_id', $this->user['service_id']]])->update(['faculty_id' => $faculty['id'], 'group_id' => 0]) !== false)
                {
                    UserRepository::instance()->clear($this->user['user_id'].'_'.Service::VK);
                    $this->user = UserRepository::instance()->get($this->user['user_id'], Service::VK);
                }
                else
                {
                    $this->text = "Произошла ошибка.\nПопробуйте позже.\n".self::SEPARATOR."\n";
                }
                $this->text = $this->view();
            }
            else
            {
                $this->text = "Я не смог найти твой факультет \xF0\x9F\x98\x94\nПришли еще раз или отправь цифру 100 для отмены выбора.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function groupView()
    {
        if($this->user['faculty_id'] != 0)
        {
            $f = FacultyRepository::instance()->get($this->user['faculty_id']);
            $this->text = "Группы факультета '{$f['short_name']} - {$f['full_name']}'\n\n";
            foreach ($f['groups'] as $group)
            {
                $this->text .= $group['id'].'. '.$group['short_name']."\n";
            }
            $this->text .= "\n\nПришлите название группы из списка или цифру.\nДля отмены выбора отправьте 1000.";
            $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::SCHEDULE_GROUP_SELECT);
        }
        else
        {
            $this->text = "Вы не выбрали факультет\n".self::SEPARATOR."\n".$this->view(false);
        }
        return $this->text;
    }

    public function selectGroup()
    {
        if($this->message != 1000)
        {
            if($group = (is_numeric($this->message) ? GroupRepository::instance()->get((int)$this->message) : GroupRepository::instance()->getByName($this->message)))
            {
                if($group['faculty_id'] == $this->user['faculty_id'])
                {
                    if(User::where([['user_id', $this->user['user_id']], ['service_id', $this->user['service_id']]])->update(['group_id' => $group['id']]) !== false)
                    {
                        UserRepository::instance()->clear($this->user['user_id'].'_'.Service::VK);
                        $this->user = UserRepository::instance()->get($this->user['user_id'], Service::VK);
                        $this->text = $this->view();
                    }
                    else
                    {
                        $this->text = "Произошла ошибка\nПопробуйте ещё раз.";
                    }
                }
                else
                {
                    $this->text = $this->user['first_name'].", этой группы нет на вашем факультете.\nПришлите верное название или цифру.\nОтправь цифру 1000, чтобы выйти из выбора группы.";
                }
            }
            else
            {
                $this->text = "Я не смог найти эту группу \xF0\x9F\x98\x94\nПришлите еще раз или отправьте цифру 1000, чтобы выйти из выбора группы.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function today($menu = true)
    {
        if($this->user['faculty_id'] != 0)
        {
            if($this->user['group_id'] != 0)
            {
                $date = date('d-m-Y');
                $subjects = GroupRepository::instance()->getActiveSubjectDay($this->user['group_id'], Carbon::now()->dayOfWeek);
                if($subjects['subjects'])
                {
                    $this->text .= "\xE2\xAD\x90 ".$subjects['week']['name']." \xE2\xAD\x90\n" .
                        "\xF0\x9F\x8C\x8D ".$subjects['day']['name']." ({$date})\n\n";
                    foreach ($subjects['subjects'] as $subject)
                    {
                        $subject['time']['id'] = isset($subject['time']['id']) ? $subject['time']['id'] : 'Неизвестно ';
                        $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                        if(is_numeric($subject['time']['id']))
                        {
                            for ($h = 0; $h < $subject['time']['id']; $h++) {
                                $this->text .= "\xE2\x8F\xB0 ";
                            }
                        }
                        else
                        {
                            $this->text .= $subject['time']['id'];
                        }
                        $this->text .= "({$subject['time']['name']})" .
                            "\nДисциплина: " .$subject['name'] . "\n" .
                            "Адрес: {$subject['address']['name']}\n" .
                            "Преподаватель: {$subject['teacher']['name']}\n\n";
                    }
                }
                else
                {
                    $this->text = "\xE2\xAD\x90 ".$subjects['week']['name']." \xE2\xAD\x90 \n".$this->user['first_name'].", у Вас сегодня нет занятий \xF0\x9F\x98\x82\n\n";
                }
            }
            else
            {
                $this->text = "Вы не выбрали группу.\n".self::SEPARATOR."\n";
            }
        }
        else
        {
            $this->text = "Вы не выбрали факультет.\n".self::SEPARATOR."\n";
        }
        if($menu)
        {
            return $this->view(false);
        }
        return $this->text;
    }

    public function tomorrowDay()
    {
        if($this->user['faculty_id'] != 0)
        {
            if($this->user['group_id'] != 0)
            {
                $date = date('d-m-Y', strtotime("+1 days"));
                $dayOfWeek = Carbon::now()->dayOfWeek;
                if($dayOfWeek == 6)
                {
                    $dayOfWeek = 0;
                    $this->text = $this->user['first_name'].", завтра Воскресенье. \xF0\x9F\x91\x91\nПоэтому я покажу тебе занятия за понедельник\n\n";
                }
                else
                {
                    $dayOfWeek++;
                    $this->text = $this->user['first_name'].", вот твои занятия на завтра \xF0\x9F\x8E\x88\n\n";
                }
                $subjects = GroupRepository::instance()->getActiveSubjectDay($this->user['group_id'], $dayOfWeek);
                if($subjects['subjects'])
                {
                    $this->text .= "\xE2\xAD\x90 ".$subjects['week']['name'] . " \xE2\xAD\x90\n" .
                        "\xF0\x9F\x8C\x8D ".$subjects['day']['name'] . " ({$date})\n\n";
                    foreach ($subjects['subjects'] as $subject)
                    {
                        $subject['time']['id'] = isset($subject['time']['id']) ? $subject['time']['id'] : 'Неизвестно ';
                        $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                        if(is_numeric($subject['time']['id']))
                        {
                            for ($h = 0; $h < $subject['time']['id']; $h++) {
                                $this->text .= "\xE2\x8F\xB0 ";
                            }
                        }
                        else
                        {
                            $this->text .= $subject['time']['id'];
                        }
                        $this->text .= "({$subject['time']['name']})" .
                            "\nДисциплина: " .$subject['name'] . "\n" .
                            "Адрес: {$subject['address']['name']}\n" .
                            "Преподаватель: {$subject['teacher']['name']}\n\n";
                    }
                }
                else
                {
                    $this->text = "\xE2\xAD\x90 ".$subjects['week']['name'] . " \xE2\xAD\x90 \n" .
                        $this->user['first_name'] . ", у Вас завтра нет занятий \xF0\x9F\x98\x82\n\n";
                }
            }
            else
            {
                $this->text = "Вы не выбрали группу.\n".self::SEPARATOR."\n";
            }
        }
        else
        {
            $this->text = "Вы не выбрали факультет.\n".self::SEPARATOR."\n";
        }
        return $this->view(false);
    }

    public function week()
    {
        if($this->user['faculty_id'] != 0)
        {
            if($this->user['group_id'] != 0)
            {
                $subjects = GroupRepository::instance()->getActiveSubjectWeek($this->user['group_id']);
                if($subjects['subjects'])
                {
                    $message = [];
                    $this->text = "\xE2\xAD\x90 ".WeekRepository::instance()->active()['name']." \xE2\xAD\x90 \n\n";
                    foreach ($subjects['subjects'] as $subject)
                    {
                        $subject['time']['id'] = isset($subject['time']['id']) ? $subject['time']['id'] : 'Неизвестно';
                        $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                        $text1 = '';
                        for ($h = 0; $h < $subject['time']['id']; $h++) {
                            $text1 .= "\xE2\x8F\xB0 ";
                        }
                        $message[$subject['day']['name']][] = $text1 . " ({$subject['time']['name']})" .
                            "\nДисциплина: " .$subject['name'] . "\n" .
                            "Адрес: {$subject['address']['name']}\n" .
                            "Преподаватель: {$subject['teacher']['name']}\n";
                    }
                    for($i = 0, $keys = array_keys($message), $j = count($keys); $i < $j; $i++)
                    {
                        $this->text .= "\xF0\x9F\x8C\x8D ".$keys[$i]."\n";
                        foreach ($message[$keys[$i]] as $item)
                        {
                            $this->text .= $item;
                        }
                        $this->text .= "\n";
                    }
                }
                else
                {
                    $this->text = $subjects['week']['name'] . " \xF0\x9F\x98\x8F\n" .
                        $this->user['first_name'] . ", у Вас на этой недели нет занятий \xF0\x9F\x98\x82\n*********************\n" . $this->mainMenu();
                }
            }
            else
            {
                $this->text = "Вы не выбрали группу.\n".self::SEPARATOR."\n";
            }
        }
        else
        {
            $this->text = "Вы не выбрали факультет.\n".self::SEPARATOR."\n";
        }
        return $this->view(false);
    }

    public function call()
    {
        if($this->user['group_id'] != 0)
        {
            $t = $this->user['call'] == 0 ? 'включены' : 'выключены';
            $m = $this->user['call'] == 0 ? 'выключить' : 'включить';
            $com = '1. '.($this->user['call'] == 0 ? "\xE2\x9D\x8E Выключить" : "\xE2\x9C\x85 Включить");
            $this->text = "\xF0\x9F\x94\x8A Уведомления (сейчас твои уведомления о расписании {$t})\n\n{$com}\n\nОтправьте цифру 1 если вы хотите {$m} уведомления.\nДля выхода отправьте цифру 100.";
            $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::SCHEDULE_CALL_SELECT);
        }
        else
        {
            $this->text = $this->user['first_name'].", для начала выбери группу\n".$this->view(false);
        }
        return $this->text;
    }

    public function selectCall()
    {
        if(self::CANCELED != $this->message)
        {
            if(self::YES == $this->message)
            {
                if(User::where([['user_id', $this->user['user_id']], ['service_id', $this->user['service_id']]])->update(['call' => $this->user['call'] == 0 ? 1 : 0]))
                {
                    UserRepository::instance()->clear($this->user['user_id'].'_'.Service::VK);
                    $this->user = UserRepository::instance()->get($this->user['user_id'], Service::VK);
                    $this->text = $this->view();
                }
                else
                {
                    $this->text = "Произошла ошибка.\nПопробуйте снова.";
                }
            }
            else
            {
                $this->text = $this->call();
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

}