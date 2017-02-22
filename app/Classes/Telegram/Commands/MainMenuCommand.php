<?php

namespace App\Classes\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class MainMenuCommand extends UserCommand
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
                ['text' => 'Расписание', 'callback_data' => 'schedule'],
                ['text' => 'Преподаватели', 'callback_data' => 'teachers']
            ]
            ,
            [
                ['text' => 'Аудитории', 'callback_data' => 'audience'],
                ['text' => 'Feedback', 'callback_data' => 'feedback']
            ]
        );
        $data = [
            'text'         => 'Главное меню',
            'reply_markup' => $inlineKeyboard
        ];
        if($this->update->getUpdateType() == 'message')
        {
            $data['chat_id'] = $this->getMessage()->getChat()->getId();
            return Request::sendMessage($data);
        }
        else
        {
            $data['chat_id'] = $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
            $data['message_id'] = $this->update->getCallbackQuery()->getMessage()->getMessageId();
            return Request::editMessageText($data);
        }
    }
}