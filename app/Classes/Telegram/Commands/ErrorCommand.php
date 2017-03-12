<?php

namespace App\Classes\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class ErrorCommand extends MyCommand
{

    protected $message;

    public function __construct(Telegram $telegram, Update $update, $message)
    {
        parent::__construct($telegram, $update);
        $this->message = $message;
    }

    public function execute()
    {
        return Request::sendMessage([
            'chat_id'      => $this->chatId,
            'text'         => $this->firstName.$this->message
        ]);
    }
}