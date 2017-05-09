<?php

namespace App\Support\VK\Bot;

use App\Models\Service;
use App\Repositories\UserRepository;
use App\Support\Base\BaseManager;
use App\Support\VK\Bot\Commands\BaseVKCommand;

class Manager extends BaseManager
{

    public function run()
    {
        $text = false;
        if(isset($this->user['commands']['end']))
        {
            $class = $this->user['commands']['end']['class'];
            $method = $this->user['commands']['end']['method'];
            if(class_exists($class))
            {
                $class = new $class($this->user, $this->data['object']['body'], $this->data);
                $text = $class->$method();
            }
            else
            {
                /*
                 * No Class
                 */
            }
        }
        else
        {
            $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, BaseVKCommand::MAIN_MENU_VIEW);
            $text = $this->run();
        }
        return $text;
    }

}