<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Service;
use App\Models\User;
use App\Repositories\LocationRepository;
use App\Repositories\UserRepository;
use App\Support\VK\Bot\Manager;

class PersonalAreaCommand extends BaseVKCommand
{

    protected $commands = [
        self::MAIN_MENU_VIEW,
        self::SCHEDULE_VIEW,
        2,
        self::PERSONAL_AREA_LOCATION_VIEW,
        self::PERSONAL_BROADCASTING_VIEW
    ];

    public function view($title = true)
    {
        if($this->user['location_id'] == 0)
        {
            $location = 'Не выбрано';
        }
        else
        {
            $location = LocationRepository::instance()->get($this->user['location_id'])['name'];
        }
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::PERSONAL_AREA_SELECT);
        if($title)
        {
            $this->text = "\xF0\x9F\x8E\x92 Личный кабинет\n".self::SEPARATOR."\n";
        }
        $this->text .= "1. \xF0\x9F\x93\x85 Моё расписание\n".
                      "2. \xF0\x9F\x8C\x81 СДО\n".
                      "3. \xF0\x9F\x8C\x8F Проживаю: {$location}\n".
                      "4. \xF0\x9F\x8E\xBA Вещание\n".
                      "0. \xF0\x9F\x8C\x80 Главное меню\n";
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

    public function locationView()
    {
        $this->text = "Где Вы проживаете ? \xF0\x9F\x98\x89\n\n";
        foreach (LocationRepository::instance()->all() as $location)
        {
            $this->text .= $location['id'].'. '.$location['name'];
            if($location['name'] != 'Москва')
            {
                $this->text .= "\n";
            }

        }
        $this->text .= " [Если Вы не проживаете в общежитие]\n\nПришлите цифру из списка.\nДля отмены выбора отправьте 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::PERSONAL_AREA_LOCATION_SELECT);
        return $this->text;
    }

    public function selectLocation()
    {
        if(self::CANCELED != $this->message)
        {
            if($location = LocationRepository::instance()->get($this->message))
            {
                if(User::where([['user_id', $this->user['user_id']], ['service_id', $this->user['service_id']]])->update(['location_id' => $location['id']]) !== false)
                {
                    UserRepository::instance()->clear($this->user['user_id'].'_'.Service::VK);
                    $this->user = UserRepository::instance()->get($this->user['user_id'], Service::VK);
                    $this->text = $this->view();
                }
                else
                {
                    $this->text = "Произошла ошибка.\nПопробуйте позже.\n";
                }
            }
            else
            {
                $this->text = "Я не смог найти это место.\nПришлите ещё раз или отправьте цифру 100 для отмены выбора.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function broadcastingView()
    {
        $this->text =  "\xF0\x9F\x8E\xBA Вещание - отправляйте сообщения от имени Бота своим сокурсникам.\n".
                       "Введите сообщение [Например: Привет всем кто с ФИТ \xE2\x9C\x8C ]\n".
                       "Для отмены отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::PERSONAL_BROADCASTING_SELECT);
        return $this->text;

    }

    public function selectBroadcasting()
    {
        if(self::CANCELED != $this->message)
        {

        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

}