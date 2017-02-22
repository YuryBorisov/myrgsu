<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.02.17
 * Time: 8:25
 */

namespace App\Classes\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class SettingsCommand extends UserCommand
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
           ]
        );
        $data = [
            'chat_id'      => $chat_id,
            'text'         => '123',
            'reply_markup' => $inline_keyboard,
            'message_id' => $this->update->getCallbackQuery()->getMessage()->getMessageId()
        ];
        return Request::editMessageText($data);
    }
}