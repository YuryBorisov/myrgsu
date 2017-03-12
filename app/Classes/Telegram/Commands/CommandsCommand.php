<?php

namespace App\Classes\Telegram\Commands;


use Longman\TelegramBot\Request;

class CommandsCommand extends MyCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        return Request::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $this->afterMessage(false)
        ]);
    }
}