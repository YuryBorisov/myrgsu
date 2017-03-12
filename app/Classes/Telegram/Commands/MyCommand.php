<?php

namespace App\Classes\Telegram\Commands;

use App\Repositories\UserTelegramRepository;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;

abstract class MyCommand extends UserCommand
{

    protected $chatId;

    protected $firstName;

    protected $lastName;

    protected $typeMessage;

    protected $messageId;

    protected $user;

    public function __construct(Telegram $telegram, Update $update = null)
    {
        parent::__construct($telegram, $update);
        if(($this->typeMessage = $this->update->getUpdateType()) == ConstantCommand::MESSAGE)
        {
            $this->chatId = $this->update->getMessage()->getChat()->getId();
            $this->firstName = $this->update->getMessage()->getChat()->getFirstName();
            $this->lastName = $this->update->getMessage()->getChat()->getLastName();
        }
        else
        {
            $this->chatId = $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
            $this->firstName = $this->update->getCallbackQuery()->getMessage()->getChat()->getFirstName();
            $this->lastName = $this->update->getCallbackQuery()->getMessage()->getChat()->getLastName();
            $this->messageId = $this->update->getCallbackQuery()->getMessage()->getMessageId();
        }
        $this->reloadUser();
    }

    public function afterMessage($transfer = true)
    {
        $message = ($transfer ?  "\n\n" : ""). "/menu - Главное меню";
        if($this->user['faculty_id'] != 0 && $this->user['group_id'] != 0) {
            $message .= "\n/my_schedule - Моё расписание\n/my_schedule_today - Расписание на сегодня\n/my_schedule_week - Расписание на неделю";
        }
        $message .= "\n/commands - Команды\n/teachers - Преподаватели\n/feedback - Обратная связь";
        return $message;
    }

    protected function reloadUser()
    {
        $this->user = UserTelegramRepository::instance()->get($this->chatId);
    }

}
