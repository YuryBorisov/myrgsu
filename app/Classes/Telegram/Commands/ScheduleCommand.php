<?php

namespace App\Classes\Telegram\Commands;

use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserTelegramRepository;
use Illuminate\Support\Facades\Log;
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
                $firstName = $this->update->getCallbackQuery()->getMessage()->getChat()->getFirstName();
                $message = null;
                if($userTelegram['group_id'])
                {
                    $date = explode('-', date('d-m-Y'));
                    $subjects = GroupRepository::instance()->getActiveSubjectDay($userTelegram['group_id'], date("w", mktime(0, 0, 0, $date[0], $date[1], $date[2])) - 1);
                    Log::info(date("w", mktime(0, 0, 0, $date[0], $date[1], $date[2])));
                    if($subjects['subjects'])
                    {
                        $message = $subjects['week']['name'] . "\xF0\x9F\x98\x8F\n" .
                            $subjects['day']['name'] . " ({$date[0]}-{$date[1]}-{$date[2]})\n\n";

                        foreach ($subjects['subjects'] as $subject)
                        {
                            $message .= $subject['time']['id'] . " ({$subject['time']['name']})" .
                            "\nДисциплина: " .$subject['name'] . "\n" .
                            "Адрес: {$subject['address']['name']}\nАудитория: {$subject['address']['room']}\n" .
                            "Преподаватель: {$subject['teacher']['name']}\n\n";
                        }
                        $message .= 'Вызвать меню /menu';
                    }
                    else
                    {
                        $message = $subjects['week']['name'] . "\xF0\x9F\x98\x8F\n" .
                            $subjects['day']['name'] . " ({$date[0]}-{$date[1]}-{$date[2]})\n".
                            $firstName . ", у Вас сегодня нет занятий \xF0\x9F\x98\x82";
                    }
                }
                else
                {
                    $message = $firstName .', для начала выбери группу';
                }
                Request::sendMessage([
                    'text' => $message,
                    'chat_id' => $chatId
                ]);
                break;
            case ConstantCommand::MY_SCHEDULE_WEEK:
                break;
        }
    }
}