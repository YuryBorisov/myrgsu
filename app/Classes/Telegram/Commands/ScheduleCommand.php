<?php

namespace App\Classes\Telegram\Commands;

use App\Repositories\FacultyRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserTelegramRepository;
use App\Repositories\WeekRepository;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class ScheduleCommand extends MyCommand
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
                $userTelegram = UserTelegramRepository::instance()->get($this->chatId);
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
                                'callback_data' => 'my_schedule_week'
                            ]
                        ];
                        /*
                        $arr[] = [
                            [
                                'text' => ($userTelegram['call'] ? 'ВЫКЛ' : 'ВКЛ') . ' уведомления',
                                'callback_data' => 'call_schedule',
                            ]
                        ];
                        */
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
                if(ConstantCommand::MESSAGE == $this->typeMessage)
                {
                    return Request::sendMessage([
                        'chat_id'      => $this->chatId,
                        'text'         => $text,
                        'reply_markup' => (new \ReflectionClass(InlineKeyboard::class))->newInstanceArgs($arr)
                    ]);
                }
                else
                {
                    return Request::editMessageText([
                        'chat_id'      => $this->chatId,
                        'text'         => $text,
                        'reply_markup' => (new \ReflectionClass(InlineKeyboard::class))->newInstanceArgs($arr),
                        'message_id' => $this->update->getCallbackQuery()->getMessage()->getMessageId()
                    ]);
                }
                break;
            case ConstantCommand::MY_SCHEDULE_TODAY:
                $userTelegram = UserTelegramRepository::instance()->get($this->chatId);
                $message = null;
                if($userTelegram['group_id'])
                {
                    $date = explode('-', date('d-m-Y'));
                    $subjects = GroupRepository::instance()->getActiveSubjectDay($userTelegram['group_id'], \Carbon\Carbon::now()->dayOfWeek);
                    if($subjects['subjects'])
                    {
                        $message = '<b>' . $subjects['week']['name'] . "</b>\xF0\x9F\x98\x8F\n" .
                            $subjects['day']['name'] . " ({$date[0]}-{$date[1]}-{$date[2]})\n\n";
                        foreach ($subjects['subjects'] as $subject)
                        {
                            $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                            $message .= '<b>' . $subject['time']['id'] . "</b> ({$subject['time']['name']})" .
                            "\n<b>Дисциплина:</b> " .$subject['name'] . "\n" .
                            "<b>Адрес:</b> {$subject['address']['name']}\n" .
                            "<b>Преподаватель:</b> {$subject['teacher']['name']}\n\n";
                        }
                        $message .= $this->afterMessage();
                    }
                    else
                    {
                        $message = '<b>' . $subjects['week']['name'] . "</b>\xF0\x9F\x98\x8F\n" .
                            $this->firstName . ", у Вас сегодня нет занятий \xF0\x9F\x98\x82" . $this->afterMessage();
                    }
                }
                else
                {
                    $message = $this->firstName .', для начала выбери группу' .$this->afterMessage();
                }
                Request::sendMessage([
                    'text' => $message,
                    'chat_id' => $this->chatId,
                    'parse_mode' => 'HTML'
                ]);
                break;
            case ConstantCommand::MY_SCHEDULE_WEEK:
                $message = 'Вы не выбрали группу';
                if($this->user['group_id'] != 0)
                {
                    $subjects = GroupRepository::instance()->getActiveSubjectWeek($this->user['group_id']);
                    if($subjects['subjects'])
                    {

                        //$message = '<b>' . $subjects['week']['name'] . "</b>\xF0\x9F\x98\x8F\n";
                        $message = [];
                        $text = WeekRepository::instance()->active()['name']." \xF0\x9F\x98\x8F\n\n";
                        foreach ($subjects['subjects'] as $subject)
                        {
                            $subject['time']['name'] = isset($subject['time']['name']) ? $subject['time']['name'] : 'Неизвестно';
                            $message[$subject['day']['name']][] = '<b>' . $subject['time']['id'] . "</b> ({$subject['time']['name']})" .
                                "\n<b>Дисциплина:</b> " .$subject['name'] . "\n" .
                               "<b>Адрес:</b> {$subject['address']['name']}\n" .
                                "<b>Преподаватель:</b> {$subject['teacher']['name']}\n";
                        }
                        for($i = 0, $keys = array_keys($message), $j = count($keys); $i < $j; $i++)
                        {
                            $text .= "<b>".$keys[$i]."</b>\n";
                            foreach ($message[$keys[$i]] as $item)
                            {
                                $text .= $item;
                            }
                            $text .= "\n";
                        }
                        $message = $text.$this->afterMessage(false);
                    }
                    else
                    {
                        $message = '<b>' . $subjects['week']['name'] . "</b>\xF0\x9F\x98\x8F\n" .
                            $this->firstName . ", у Вас на этой недели нет занятий \xF0\x9F\x98\x82" . $this->afterMessage();
                    }
                }
                return Request::sendMessage([
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);
                break;
        }
    }
}