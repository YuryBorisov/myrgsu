<?php

namespace App\Classes\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class TeachersCommand extends UserCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $chat_id = $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
        $inline_keyboard = new InlineKeyboard(
            [
                ['text' => 'Назад', 'callback_data' => 'back_main_menu']
            ],
            [
                ['text' => 'Факультет: Не выбран', 'callback_data' => 'my_schedule_faculty']
            ],
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ],
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Группа: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
            ,
            [
                ['text' => 'Груп11па: Не выбрана', 'callback_data' => 'my_schedule_group']
            ]
        );
        $data = [
            'chat_id'      => $chat_id,
            'text'         => 'Моё расписание',
            'reply_markup' => $inline_keyboard,
            'message_id' => $this->update->getCallbackQuery()->getMessage()->getMessageId()
        ];
        return Request::editMessageText($data);
    }
}