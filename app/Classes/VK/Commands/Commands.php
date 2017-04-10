<?php

namespace App\Classes\VK\Commands;

use App\Models\UserVK;
use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserVKRepository;
use App\Repositories\WeekRepository;
use Carbon\Carbon;

class Commands
{

    private $command;

    private $user;

    private $message;

    private $commands = [
        'mainMenu', // MainMenu 0
        'mySchedule', // 1
        'rooms',// 2
        'teachers', // 3
        'feedback', // 4
        'faculty', // 5
        'group', //6
        'myScheduleToday', // 7
        'myScheduleWeek', // 8
        55 => 'selectFacultyClose',
        66 => 'selectGroupClose'
     ];

    public function __construct($user, $command, $message = null)
    {
        $this->command = $command;
        $this->user = $user;
        $this->message = $message;
    }

    public function executeCommandText() {
        if($this->command == 'select_faculty') {
            if($faculty = FacultyRepository::instance()->getByName($this->message)) {
                if($this->user['faculty_id'] != $faculty['id']) {
                    if(UserVK::where(['id' => $this->user['id']])->update(['faculty_id' => $faculty['id'], 'group_id' => 0])) {
                        UserVKRepository::instance()->clear($this->user['id']);
                        $this->user = UserVKRepository::instance()->get($this->user['id']);
                        $text = $this->selectFacultyClose();
                    } else {
                        $text = "Произошла ошибка\nПопробуйте ещё раз.";
                    }
                } else {
                    $text = $this->selectFacultyClose();
                }
            } else {
                $text = "Я не смог найти твой факультет \xF0\x9F\x98\x94\nПришли еще раз или отправь цифру '55' для отмены выбора";
            }
            return $text;
        } else if($this->command == 'select_group'){
            if($group = GroupRepository::instance()->getByName($this->message))
            {
                if($group['faculty_id'] == $this->user['faculty_id'])
                {
                    if(UserVK::where(['id' => $this->user['id']])->update(['group_id' => $group['id']]))
                    {
                        UserVKRepository::instance()->clear($this->user['id']);
                        $this->user = UserVKRepository::instance()->get($this->user['id']);
                        $text = $this->selectGroupClose();
                    }
                    else
                    {
                        $text = "Произошла ошибка\nПопробуйте ещё раз.";
                    }
                }
                else
                {
                    $text = $this->user['first_name'].", этой группы нет на вашем факультете\nПришли верное название отправь цифру '66', чтобы выйти из выбора группы";
                }
            }
            else
            {
                $text = "Я не смог найти эту группу \xF0\x9F\x98\x94\nПришли еще раз или отправь цифру '66', чтобы выйти из выбора группы";
            }
            return $text;
        }
    }

    public function executeCommandNumber()
    {
        if(!isset($this->commands[$this->command]))
        {
            return "Нет такой команды\n*********************\n" . $this->mainMenu();
        }
        return $this->{$this->commands[$this->command]}();
    }

    private function mainMenu()
    {
        return "1. \xF0\x9F\x9A\x80 Моё расписание\n2. \xF0\x9F\x9B\x80 Аудитории\n3. \xF0\x9F\x91\xBA Преподаватели\n4. \xF0\x9F\x8E\xA4 Feedback \n";
    }

    private function mySchedule()
    {
        $text = "\xF0\x9F\x9A\x80 Моё расписание\n\n";
        if(isset($this->user['faculty_id']) && $this->user['faculty_id'] != 0)
        {
            $faculty = FacultyRepository::instance()->get($this->user['faculty_id']);
            $text .= "5. \xE2\x98\x9D Факультет: {$faculty['short_name']}\n";
            if ($this->user['group_id'] != 0)
            {
                foreach ($faculty['groups'] as $group)
                {
                    if($group['id'] == $this->user['group_id'])
                    {
                        $text .= "6. \xF0\x9F\x8E\x93 Группа: {$group['short_name']}\n7. \xF0\x9F\x8E\x89 Сегодняшние занятия\n8. \xF0\x9F\x8E\x8A Показать за неделю\n\n0. \xE2\xAC\x85 Главное меню";
                        break;
                    }
                }
            }
            else
            {
                $text .= "6. \xF0\x9F\x8E\x93 Группа: Не выбрана\n\n0. \xE2\xAC\x85 Главное меню";
            }
        }
        else
        {
            $text .= "5. \xE2\x98\x9D Факультет: Не выбран\n\n0. \xE2\xAC\x85 Главное меню";
        }
        return $text;
    }

    private function faculty()
    {
        $faculties = FacultyRepository::instance()->all();
        $text =  "55. \xE2\xAC\x85 Вернуться назад\n\nПришлите сокращённое название факультета\n\nСписок факультетов:\n";
        foreach ($faculties as $faculty) {
            $text .= "{$faculty['short_name']} - {$faculty['full_name']}\n";
        }
        UserVKRepository::instance()->addCommandEnd($this->user['id'], 'select_faculty');
        return $text;
    }

    private function selectFacultyClose() {
        UserVKRepository::instance()->addCommandEnd($this->user['id'], false);
        return $this->mySchedule();
    }

    private function group()
    {
        if($this->user['faculty_id'] != 0) {
            $f = FacultyRepository::instance()->get($this->user['faculty_id']);
            $text = "Группы факультета '{$f['short_name']} - {$f['full_name']}'\n\n";
            foreach ($f['groups'] as $group)
            {
                $text .= $group['short_name']."\n";
            }
            $text .= "\n\nПришлите название группы из списка.\nДля отмены выбора отправьте 66";
            UserVKRepository::instance()->addCommandEnd($this->user['id'], 'select_group');
        }
        else
        {
            $text = $this->faculty();
        }
        return $text;
    }

    private function selectGroupClose()
    {
        UserVKRepository::instance()->addCommandEnd($this->user['id'], false);
        return $this->mySchedule();
    }

    private function myScheduleToday()
    {
        if($this->user['group_id'] != 0)
        {
            $date = explode('-', date('d-m-Y'));
            //Carbon::now()->dayOfWeek
            $subjects = GroupRepository::instance()->getActiveSubjectDay($this->user['group_id'], Carbon::now()->dayOfWeek);
            if($subjects['subjects'])
            {
                $text = "\xE2\xAD\x90 ".$subjects['week']['name'] . " \xE2\xAD\x90\n" .
                    "\xF0\x9F\x8C\x8D ".$subjects['day']['name'] . " ({$date[0]}-{$date[1]}-{$date[2]})\n\n";
                foreach ($subjects['subjects'] as $subject)
                {
                    $subject['time']['id'] = isset($subject['time']['id']) ? $subject['time']['id'] : 'Неизвестно';
                    $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                    for ($h = 0; $h < $subject['time']['id']; $h++) {
                        $text .= "\xE2\x8F\xB0 ";
                    }
                    $text .= "({$subject['time']['name']})" .
                        "\nДисциплина: " .$subject['name'] . "\n" .
                        "Адрес: {$subject['address']['name']}\n" .
                        "Преподаватель: {$subject['teacher']['name']}\n\n";
                }
                $text .= "*********************\n" . $this->mainMenu();
            }
            else
            {
                $text = "\xE2\xAD\x90 ".$subjects['week']['name'] . " \xE2\xAD\x90 \n" .
                    $this->user['first_name'] . ", у Вас сегодня нет занятий \xF0\x9F\x98\x82";
            }
        }
        else
        {
            $text = $this->user['first_name'].", для начала выбери группу";
        }
        return $text;
    }

    private function myScheduleWeek()
    {
        if($this->user['group_id'] != 0)
        {
            $subjects = GroupRepository::instance()->getActiveSubjectWeek($this->user['group_id']);
            if($subjects['subjects'])
            {
                //$message = '<b>' . $subjects['week']['name'] . "</b>\xF0\x9F\x98\x8F\n";
                $message = [];
                $text = "\xE2\xAD\x90 ".WeekRepository::instance()->active()['name']." \xE2\xAD\x90 \n\n";
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
                    $text .= "\xF0\x9F\x8C\x8D ".$keys[$i]."\n";
                    foreach ($message[$keys[$i]] as $item)
                    {
                        $text .= $item;
                    }
                    $text .= "\n";
                }
                $text .= "*********************\n" . $this->mainMenu();
            }
            else
            {
                $text = $subjects['week']['name'] . " \xF0\x9F\x98\x8F\n" .
                    $this->user['first_name'] . ", у Вас на этой недели нет занятий \xF0\x9F\x98\x82\n*********************\n" . $this->mainMenu();
            }
        }
        else
        {
            $text = $this->user['first_name'].", для начала выбери группу";
        }
        return $text;
    }

    private function feedback()
    {
        return "Есть предложения или замечания? \xF0\x9F\x98\x89\nПиши https://vk.com/id333114129\n*********************\n" . $this->mainMenu();
    }

    private function rooms()
    {
        return $this->user['first_name'].", пока не работает, нет времени реализовать \xF0\x9F\x98\x94\n*********************\n".$this->mainMenu();
    }

    private function teachers()
    {
        return $this->user['first_name'].", пока не работает, нет времени реализовать \xF0\x9F\x98\x94\n*********************\n".$this->mainMenu();
    }

}
