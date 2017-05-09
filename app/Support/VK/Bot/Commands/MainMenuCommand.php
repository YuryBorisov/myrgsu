<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Address;
use App\Models\Service;
use App\Repositories\UserRepository;
use App\Support\VK\Bot\Manager;
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
            $this->text = "\xF0\x9F\x8C\x80 Главное меню \n".self::SEPARATOR."\n";
        }
        return $this->text.
               "1. \xF0\x9F\x8E\x92 Личный кабинет\n".
               "2. \xF0\x9F\x9A\xAA Аудитории\n".
               "3. \xF0\x9F\x91\x94 Преподаватели\n".
               "4. \xF0\x9F\x93\xB0 Новости РГСУ\n".
               "5. \xF0\x9F\x8E\xA7 Плеер\n".
               "6. \xF0\x9F\x92\xAC Чат\n".
               "7. \xF0\x9F\x93\xA2 Рассылка [{$distribution}]\n".
               "8. \xE2\x9A\xA1 Пожелания/Улучшения";
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
        $this->text = "Пришли мне аудиторию [Например: ВП8-408 или 408] и я скажу тебе есть ли в ней кто - нибудь сегодня.\n".
            "Для отмены выбора отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_ROOM_SELECT);
        return $this->text;
    }

    public function selectRoom()
    {
        if(self::CANCELED != $this->message)
        {
            $address = Address::where('name', 'LIKE', '%'.$this->message.'%')->get();
            if($address->count() != 0)
            {
                if(is_numeric($this->message))
                {
                    
                }
                else
                {

                }
            }
            else
            {
                $this->text = "Я не смог найти эту аудиторию.\nПришлите ещё раз или отправьте цифру 100 для выхода.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

}