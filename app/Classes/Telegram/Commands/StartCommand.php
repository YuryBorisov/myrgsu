<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.02.17
 * Time: 2:47
 */

namespace App\Classes\Telegram\Commands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        return Request::sendMessage(['chat_id' => $this->getMessage()->getChat()->getId(), 'text' => 'Привет '.
        $this->getMessage()->getChat()->getFirstName() . " \xF0\x9F\x98\x8B"]);
    }
}