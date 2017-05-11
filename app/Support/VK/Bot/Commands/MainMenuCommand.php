<?php

namespace App\Support\VK\Bot\Commands;

use App\Models\Address;
use App\Models\ChatRoomUser;
use App\Models\News;
use App\Models\Service;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\ChatRoomRepository;
use App\Repositories\GroupRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\TimeRepository;
use App\Repositories\UserRepository;
use App\Repositories\WeekRepository;
use App\Support\VK\Bot\Manager;
use App\Support\VK\Bot\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MainMenuCommand extends BaseVKCommand
{

    protected $commands = [
        1 => self::PERSONAL_AREA_VIEW,
        2 => self::MAIN_MENU_ROOM_VIEW,
        3 => self::MAIN_MENU_TEACHERS_VIEW,
        4 => self::MAIN_MENU_NEWS_VIEW,
        5 => self::MAIN_MENU_PLAYER_VIEW,
        6 => self::MAIN_MENU_CHAT_VIEW,
        7 => self::MAIN_MENU_DISTRIBUTION_VIEW,
        8 => self::MAIN_MENU_WISHES_VIEW,
        9 => self::MAIN_MENU_FEEDBACK_VIEW
    ];

    public function view($title = true)
    {
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_SELECT);
        $distribution = $this->user['distribution'] == 0 ? 'ВКЛ' : 'ВЫКЛ';
        if($title)
        {
            $this->text .= "\xF0\x9F\x8C\x80 Главное меню \n".self::SEPARATOR."\n";
        }
        $this->text .= "1. \xF0\x9F\x8E\x92 Личный кабинет\n".
                       "2. \xF0\x9F\x9A\xAA Аудитории\n".
                       "3. \xF0\x9F\x91\x94 Преподаватели\n".
                       "4. \xF0\x9F\x93\xB0 Новости РГСУ\n".
                       "5. \xF0\x9F\x8E\xA7 Плеер\n".
                       "6. \xF0\x9F\x92\xAC Чат\n".
                       "7. \xF0\x9F\x93\xA2 Рассылка [{$distribution}]\n".
                       "8. \xE2\x9A\xA1 Пожелания/Улучшения\n".
                       "9. \xF0\x9F\x8E\xA4 Feedback";
        return $this->text;
    }

    public function mainSelect()
    {
        if(isset($this->commands[$this->message]))
        {
            $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, $this->commands[$this->message]);
            $this->text = (new Manager($this->user, $this->data))->run();
        }
        else
        {
            $this->text = self::NOT_COMMAND.$this->view(false);
        }
        return $this->text;
    }

    public function roomView()
    {
        $this->text = "Пришли мне аудиторию [Например: ВП8-408] и я скажу тебе есть ли в ней кто - нибудь сегодня.\n".
            "Для отмены выбора отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_ROOM_SELECT);
        return $this->text;
    }

    private function searchRoomSubject($subjects, $timeId)
    {
        foreach ($subjects as $s)
        {
            if($s['time_id'] == $timeId)
            {
                for ($h = 0; $h < $timeId; $h++) {
                    $this->text .= "\xE2\x8F\xB0 ";
                }
                $time = TimeRepository::instance()->get($timeId)['name'];
                return "(".$time.")\n".
                              "Группа: ".GroupRepository::instance()->get($s['group_id'])['short_name']."\n".
                              "Дисциплина: ".$s['name']."\n".
                              "Преподаватель: ".TeacherRepository::instance()->get($s['teacher_id'])['name']."\n\n";
            }
        }
    }

    public function selectRoom()
    {
        if(self::CANCELED != $this->message)
        {
            $arr = explode('-', $this->message);
            if(isset($arr[0]) && isset($arr[1]))
            {
                $address = Address::where('name', 'LIKE', '%'.$this->message.'%')->get();
                if($address->count() != 0)
                {
                    $ids = [];
                    foreach ($address as $a)
                    {
                        $ids[] = $a['id'];
                    }
                    $weekId = WeekRepository::instance()->active()['id'];
                    $subject = Subject::whereIn('address_id', $ids)->whereWeekId($weekId)->whereDayId(Carbon::now()->dayOfWeek)->get();
                    $this->text .= "Сегодня: ".date('d-m-Y')."\nАудитория: ".$this->message."\n\n";
                    for($i = 1; $i <= 10; $i++)
                    {
                        $this->text .= $this->searchRoomSubject($subject, $i);
                    }
                    $this->view(false);
                }
                else
                {
                    $this->text = "Я не смог найти эту аудиторию.\nПришлите ещё раз или отправьте цифру 100 для выхода.";
                }
            }
            else
            {
               $this->text = "Не верный формат.\nПример правильного формата: ВП8-408\nПришлите еще раз или отправьте цифру 100 для выхода.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function teachersView()
    {
        $this->text = "Пришли мне фамилию любого преподавателя и я скажу тебе где он сегодня. \xF0\x9F\x98\x89 \xF0\x9F\x98\x82\n".
                      "Для отмены выбора отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_TEACHERS_SELECT);
        return $this->text;
    }

    public function selectTeachers()
    {
        if(self::CANCELED != $this->message)
        {
            if(is_numeric($this->message))
            {
                if($teacher = Teacher::find($this->message))
                {
                    $sub = Subject::whereTeacherId($teacher->id)->whereDayId(Carbon::now()->dayOfWeek)->whereWeekId(WeekRepository::instance()->active()['id'])->get();
                    if($sub->count() > 0)
                    {
                        $this->text .= "Сегодня: ".date('d-m-Y')."\nПреподаватель: ".$teacher->name.".\n\n";
                        foreach ($sub as $s)
                        {
                            for ($h = 0; $h < $s->time_id; $h++) {
                                $this->text .= "\xE2\x8F\xB0 ";
                            }
                            $time = TimeRepository::instance()->get($s->time_id)['name'];
                            $address = AddressRepository::instance()->get($s['address_id']);
                            $this->text .= "(".$time.")\n".
                                "Группа: ".GroupRepository::instance()->get($s['group_id'])['short_name']."\n".
                                "Дисциплина: ".$s['name']."\n".
                                "Адрес: ".$address['name']."\n\n";
                        }
                    }
                    else
                    {
                        $this->text = $teacher->name." сегодня не проводит занятия.\n".self::SEPARATOR."\n";
                    }
                    $this->text = $this->view(false);
                }
                else
                {
                    $this->text = "Я не смог никого найти \xF0\x9F\x98\x93\nПришлите еще раз или отправьте цифру 100 для выхода.";
                }
            }
            else
            {
                $t = explode(' ', $this->message)[0];
                if(mb_strlen($t) >= 3)
                {
                    $teacher = Teacher::where('name', 'LIKE', '%'.$t.'%')->get();
                    if($teacher->count() > 0)
                    {
                        $this->text = "Вот, кого я нашел в базе.\n\n";
                        foreach ($teacher as $t)
                        {
                            $this->text .= $t['id'].'. '.$t['name']."\n";
                        }
                        $this->text .= "\nПришлите мне цифру или отправьте 100 для выхода.";
                    }
                    else
                    {
                        $this->text = "Я не смог никого найти \xF0\x9F\x98\x93\nПришлите еще раз или отправьте цифру 100 для выхода.";
                    }
                }
                else
                {
                    $this->text = "Ваше сообщение должно быть от 3 символов.\nПришлите еще раз или отправьте цифру 100 для выхода.";
                }
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function newsView()
    {
        $news = News::orderBy('created_at', 'desk')->limit(3)->get();
        $this->text = "\xF0\x9F\x93\xB0 Последние новости.\n\n";
        foreach ($news as $n)
        {
            $date = explode('-', explode(' ', $n->created_at)[0]);
            $this->text .= "\xF0\x9F\x93\x85 Дата: ".$date[2].'-'.$date[1].'-'.$date[0]."\n";
            $this->text .= "\xF0\x9F\x93\x92 Заголовок: ".$n->title."\n";
            $this->text .= "\xF0\x9F\x93\x96 Краткое описание: ".$n->short_text."\n";
            $this->text .= "\xF0\x9F\x93\xA2 Источник: ".$n->link."\n\n";
        }
        $this->view(false);
        return $this->text;
    }

    public function playerView()
    {
        $response = json_decode(Request::getComments(), true);
        if(isset($response['response']))
        {
            $response = $response['response'];
            unset($response['comments'][0]);
            $audios = [];
            foreach ($response['comments'] as $comment)
            {
                if(isset($comment['attachments']))
                {
                    foreach ($comment['attachments'] as $attachment)
                    {
                        if($attachment['type'] == 'audio')
                        {
                            $attachment['user_id'] = $comment['from_id'];
                            $audios[] =  $attachment;
                        }
                    }
                }
            }
            $audio = $audios[rand(0, count($audios) - 1)];
            if(!$userAudio = UserRepository::instance()->get($audio['user_id'], Service::VK))
            {
                $userAudio = json_decode($t = file_get_contents("https://api.vk.com/method/users.get?user_ids={$audio['user_id']}&v=5.0"), true)['response'][0];
            }
            $arr = [
                'user_id' => $this->user['user_id'],
                'message' => "Добавил(a): {$userAudio['first_name']} {$userAudio['last_name']} [https://vk.com/id{$audio['user_id']}]\n",
                'access_token' => env('VK_BOT_KEY'),
                'attachment' => 'audio'.$audio['audio']['owner_id'].'_'.$audio['audio']['aid'],
                'v' => '5.0'
            ];
            Request::sendAttachment($arr);
            $this->text = "Добавь свою музыку \xF0\x9F\x8E\xA7\nhttps://vk.com/topic-144482898_35459441\nОтправь цифру 5, чтобы получить ещё песню.\n".self::SEPARATOR."\n".$this->view(false);
        }
        else
        {
            $this->text = "Произошла ошибка.\nСообщите администратору.\n".self::SEPARATOR."\n".$this->view(false);
        }
        return $this->text;
    }

    public function distributionView()
    {
        $t = $this->user['distribution'] == 0 ? 'включена' : 'выключена';
        $m = $this->user['distribution'] == 0 ? 'выключить' : 'включить';
        $com = '1. '.($this->user['distribution'] == 0 ? "\xE2\x9D\x8E Выключить" : "\xE2\x9C\x85 Включить");
        $text = "\xF0\x9F\x94\x8A Рассылка (сейчас твоя рассылка на новости проекта и о разных событиях {$t})\n\n{$com}\n\nОтправьте цифру 1 если вы хотите {$m} рассылку.\nДля выхода отправьте цифру 100.";
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_DISTRIBUTION_SELECT);
        return $text;
    }

    public function selectDistribution()
    {
        if(self::CANCELED != $this->message)
        {
            if (self::YES == $this->message)
            {
                if(User::where([['user_id', $this->user['user_id']], ['service_id', $this->user['service_id']]])->update(['distribution' => $this->user['distribution'] == 0 ? 1 : 0]) !== false)
                {
                    UserRepository::instance()->clear($this->user['user_id'].'_'.Service::VK);
                    $this->user = UserRepository::instance()->get($this->user['user_id'], Service::VK);
                    $this->text = $this->view();
                }
                else
                {
                    $this->text = "Произошла ошибка.\nПопробуйте позже\n".self::SEPARATOR."\n".$this->view(false);
                }
            }
            else
            {
                $this->text = $this->distributionView();
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function wishesView()
    {
        $this->text = "Есть предложения ? \xF0\x9F\x98\x89\nНапиши сюда https://vk.com/topic-144482898_35457395\n".self::SEPARATOR."\n".$this->view(false);
        return $this->text;
    }

    public function feedbackView()
    {
        $this->text = "Не можешь разобраться? \xF0\x9F\x98\x89\nПиши https://vk.com/id333114129\n".self::SEPARATOR."\n".$this->view(false);
        return $this->text;
    }

    public function chatView()
    {
        $this->text = "\xF0\x9F\x92\xAC Чат.\n\nСписок комнат:\n";
        foreach (ChatRoomRepository::instance()->getAll() as $room)
        {
            $count = ChatRoomUser::whereRoomId($room['id'])->count();
            $text = "пользователей";
            switch ($count)
            {
                case 1:
                    $text = 'пользователь';
                    break;
                case 2:
                case 3:
                case 4:
                $text = 'пользователя';
            }
            $this->text .= "{$room['id']}. {$room['name']} [{$count} {$text}]\n";
        }
        $this->text .= "Список чатов в Telegram: https://vk.com/rgsu_bot?w=wall-144482898_8\n".
                       self::SEPARATOR."\nПришли номер комнаты или отправь цифру 100 для выхода в главное меню.";;
        $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_CHAT_SELECT);
        return $this->text;
    }

    public function selectChat()
    {
        if(self::CANCELED != $this->message)
        {
            if($room = ChatRoomRepository::instance()->get($this->message))
            {
                ChatRoomUser::whereUserId($this->user['id'])->delete();
                $users = ChatRoomUser::whereRoomId($room['id'])->get();
                ChatRoomUser::create([
                    'user_id' => $this->user['id'],
                    'room_id' => $room['id']
                ]);
                foreach ($users as $user)
                {
                    Request::sendMessage([
                        'message' => 'РГСУ БОТ: к нам присоединился '.$this->user['first_name'].' '.$this->user['last_name'],
                        'user_id' => $user->user_id,
                        'access_token' => env('VK_BOT_KEY'),
                        'v' => '5.0'
                    ]);
                }
                $this->text = "РГСУ БОТ: Вы зашли в комнату ".$room['name'].".\n".
                              "Для того, чтобы выйти из комнаты наберите ".self::CHAT_ROOM_EXIT.".\n".
                              "Для выхода в главное меню наберите ".self::CHAT_EXIT.".";
                $this->user = UserRepository::instance()->addCommand($this->user['user_id'], Service::VK, self::MAIN_MENU_CHAT_MESSAGE);
            }
            else
            {
                $this->text = "Такой комнаты не существует\n".self::SEPARATOR."\nПришли номер комнаты или отправь цифру 100 для выхода в главное меню.";
            }
        }
        else
        {
            $this->text = $this->view();
        }
        return $this->text;
    }

    public function messageChat()
    {
        $room = ChatRoomUser::whereUserId($this->user['id'])->first();
        $users = ChatRoomUser::whereRoomId($room->room_id)->get();
        if(self::CHAT_ROOM_EXIT == $this->message)
        {
            $message = 'РГСУ БОТ: '.$this->user['first_name'].' '.$this->user['last_name'].' вышел из комнаты.';
            ChatRoomUser::whereUserId($this->user['id'])->delete();
            $this->text = $this->chatView();
        }
        else if (self::CHAT_EXIT == $this->message)
        {
            $message = 'РГСУ БОТ: '.$this->user['first_name'].' '.$this->user['last_name'].' вышел из комнаты.';
            ChatRoomUser::whereUserId($this->user['id'])->delete();
            $this->text = $this->view();
        }
        else
        {
            $message = $this->user['first_name'].' '.$this->user['last_name'].': '.$this->message;
        }
        foreach ($users as $user)
        {
            if ($this->user['user_id'] != $user->user_id)
            {
                Request::sendMessage([
                    'message' => $message,
                    'user_id' => $user->user_id,
                    'access_token' => env('VK_BOT_KEY'),
                    'v' => '5.0'
                ]);
            }
        }
        Request::markAsRead([
            'peer_id' => $this->user['user_id'],
            'access_token' => env('VK_BOT_KEY'),
            'v' => '5.0'
        ]);
        return $this->text;
    }

}