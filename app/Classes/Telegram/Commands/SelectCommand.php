<?php

namespace App\Classes\Telegram\Commands;

use App\Models\UserTelegram;
use App\Repositories\GroupRepository;
use App\Repositories\UserTelegramRepository;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;

class SelectCommand extends UserCommand
{

    protected $selectType;

    protected $selectId;

    public function __construct(Telegram $telegram, Update $update, $selectType, $selectId)
    {
        parent::__construct($telegram, $update);
        $this->selectType = $selectType;
        $this->selectId = $selectId;
    }

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $userTelegram = UserTelegram::firstOrNew(['id' => $this->update->getCallbackQuery()->getMessage()->getChat()->getId()]);
        switch ($this->selectType)
        {
            case ConstantCommand::SELECT_MY_SCHEDULE_GROUP:
                $userTelegram->group_id = $this->selectId;
                $userTelegram->save();
                UserTelegramRepository::instance()->clear($userTelegram->id);
                (new ScheduleCommand($this->telegram, $this->update, ConstantCommand::MY))->execute();
                break;
            case ConstantCommand::SELECT_MY_SCHEDULE_FACULTY:
                $userTelegram->faculty_id = $this->selectId;
                $userTelegram->group_id = GroupRepository::instance()->get($userTelegram->group_id)['faculty_id'] == $this->selectId
                    ? $userTelegram->group_id : 0;
                $userTelegram->save();
                UserTelegramRepository::instance()->clear($userTelegram->id);
                (new ScheduleCommand($this->telegram, $this->update, ConstantCommand::MY))->execute();
                break;
        }
    }
}