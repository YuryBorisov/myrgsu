<?php

namespace App\Classes\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class MainMenuCommand extends MyCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Моё расписание', 'callback_data' => 'my_schedule']
            ],
            [
                //['text' => 'Расписание', 'callback_data' => 'schedule'],
                ['text' => 'Аудитории', 'callback_data' => 'audience'],
                ['text' => 'Преподаватели', 'callback_data' => 'teachers']
            ]
            ,
            [
                ['text' => 'Команды', 'callback_data' => 'commands'],
                ['text' => 'Feedback', 'callback_data' => 'feedback']
            ]
        );
        $data = [
            'text'         => 'Главное меню',
            'reply_markup' => $inlineKeyboard,
            'chat_id' => $this->chatId
        ];
        if($this->typeMessage == ConstantCommand::MESSAGE)
        {
            return Request::sendMessage($data);
        }
        else
        {
            $data['message_id'] = $this->messageId;
            return Request::editMessageText($data);
        }
    }
}