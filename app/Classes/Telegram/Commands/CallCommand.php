<?php

namespace App\Classes\Telegram\Commands;

use App\Models\UserTelegram;
use App\Repositories\UserTelegramRepository;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;

class CallCommand extends UserCommand
{

    public function __construct(Telegram $telegram, Update $update)
    {
        parent::__construct($telegram, $update);
    }

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $chatId = $this->update->getCallbackQuery()->getMessage()->getChat()->getId();
        $userTelegram = ($uTgRepository = UserTelegramRepository::instance())->get($chatId);
        if($userTelegram['faculty_id'] != 0 && $userTelegram['group_id'] != 0)
        {
            UserTelegram::whereId($chatId)->update(['call' => $userTelegram['call'] ? false : true]);
            $uTgRepository->clear($chatId);
            (new ScheduleCommand($this->telegram, $this->update, ConstantCommand::MY))->execute();
        }
        else
        {

        }
    }
}