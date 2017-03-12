<?php

namespace App\Classes\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class ErrorCommand extends MyCommand
{
    public function __construct(Telegram $telegram, Update $update)
    {
        parent::__construct($telegram, $update);
    }

    public function execute()
    {
        $data = [
            'chat_id'      => $this->chatId,
            'text'         => $this->firstName.', увы я не знаю такой команды '."\xF0\x9F\x98\x82"
        ];
        return Request::sendMessage($data);
    }
}