<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.02.17
 * Time: 12:44
 */

namespace App\Classes\Telegram\Commands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class FeedbackCommand extends UserCommand
{

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        Request::sendMessage([
            'chat_id' => $this->update->getCallbackQuery()->getMessage()->getChat()->getId(),
            'text' => 'Есть предложения или замечания?' . "\xF0\x9F\x98\x89\nПиши @yury_borisov"
        ]);
    }
}