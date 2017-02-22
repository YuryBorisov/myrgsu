<?php

namespace App\Classes\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class ErrorCommand extends UserCommand
{

    protected $message;

    public function __construct(Telegram $telegram, Update $update, $message)
    {
        parent::__construct($telegram, $update);
        $this->message = $message;
    }

    public function execute()
    {
        $chatId = $this->update->getUpdateType() == 'message' ? $this->getMessage()->getChat()->getId() :
            $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
        $data = [
            'chat_id'      => $chatId,
            'text'         => $this->message
        ];
        return Request::sendMessage($data);
    }
}