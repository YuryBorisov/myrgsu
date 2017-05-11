<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Service;
use App\Repositories\UserRepository;

class WelcomeCommand extends BaseVKCommand
{

    public function view($title = true)
    {
        $this->text = "Привет {$this->user['first_name']} \xE2\x9C\x8C\nЧтобы мы могли понимать друг друга присылай мне команды \xF0\x9F\x98\xA4\nНапример сейчас ты можешь мне прислать цифру 1, тогда ты попадаешь в свой личный кабинет \xF0\x9F\x98\x9C\nИ да, подпишись на меня \xF0\x9F\x98\x8D\n";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_VIEW);
        $this->text .= self::SEPARATOR."\n".(new MainMenuCommand($this->user, $this->message, $this->data))->view(false);
        return $this->text;
    }

    public function mainSelect()
    {
        // TODO: Implement mainSelect() method.
    }

}