<?php

namespace App\Classes\Telegram\Commands;

use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserTelegramRepository;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class ScheduleCommand extends UserCommand
{

    protected $prefix;

    public function __construct(Telegram $telegram, Update $update, $prefix)
    {
        parent::__construct($telegram, $update);
        $this->prefix = $prefix;
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
        $arr = [
            [
                [
                    'text' => 'Назад', 'callback_data' => 'main_menu'
                ]
            ]
        ];
        $text = null;
        switch ($this->prefix)
        {
            case ConstantCommand::MY:
                $userTelegram = UserTelegramRepository::instance()->getById($chatId);
                if($userTelegram['faculty_id'])
                {
                    $arr[] = [
                        [
                            'text' => 'Факультет: ' . FacultyRepository::instance()->get($userTelegram['faculty_id'])['short_name'],
                            'callback_data' => 'my_faculty'
                        ]
                    ];
                    if($userTelegram['group_id'])
                    {
                        $arr[] = [
                            [
                                'text' => 'Группа: ' . GroupRepository::instance()->get($userTelegram['group_id'])['short_name'],
                                'callback_data' => 'my_group'
                            ]
                        ];
                        $arr[] = [
                            [
                                'text' => 'Сегодняшние занятия',
                                'callback_data' => 'my_schedule_today'
                            ]
                        ];
                        $arr[] = [
                            [
                                'text' => 'Показать за неделю',
                                'callback_data' => 'dsfdsf'
                            ]
                        ];
                        $arr[] = [
                            [
                                'text' => ($userTelegram['call'] ? 'ВЫКЛ' : 'ВКЛ') . ' уведомления',
                                'callback_data' => 'call_schedule',
                            ]
                        ];
                    }
                    else
                    {
                        $arr[] = [
                            [
                                'text' => 'Группа: Не выбрана',
                                'callback_data' => 'my_group'
                            ]
                        ];
                    }
                }
                else
                {
                    $arr[] = [
                        [
                            'text' => 'Факультет: Не выбран',
                            'callback_data' => 'my_faculty'
                        ],
                    ];
                    $arr[] = [
                        [
                            'text' => 'Группа: Не выбрана',
                            'callback_data' => 'my_group'
                        ]
                    ];
                }
                $text = 'Моё расписание';
                return Request::editMessageText([
                    'chat_id'      => $chatId,
                    'text'         => $text,
                    'reply_markup' => (new \ReflectionClass(InlineKeyboard::class))->newInstanceArgs($arr),
                    'message_id' => $this->update->getCallbackQuery()->getMessage()->getMessageId()
                ]);
                break;
            case ConstantCommand::MY_SCHEDULE_TODAY:
                $userTelegram = UserTelegramRepository::instance()->get($chatId);
                if($userTelegram['group_id'])
                {

                }
                else
                {

                }
                break;
            case ConstantCommand::MY_SCHEDULE_WEEK:
                break;
        }
    }
}