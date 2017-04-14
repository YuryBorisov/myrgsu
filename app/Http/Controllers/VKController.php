<?php

namespace App\Http\Controllers;

use App\Classes\VK\Commands\Commands;
use App\Models\UserVK;
use App\Repositories\UserVKRepository;
use Illuminate\Support\Facades\Log;

class VKController extends Controller
{

    public function index()
    {
        $data = json_decode($c = file_get_contents('php://input'), true);
        switch ($data['type']) {
            case 'confirmation':
                return env('VK_BOT_CONFIRMATION');
                break;
            case 'message_new':
                if (!($user = ($res = UserVKRepository::instance())->get($data['object']['user_id']))) {
                    $user = json_decode($t = file_get_contents("https://api.vk.com/method/users.get?user_ids={$data['object']['user_id']}&v=5.0"), true);
                    if ($user =  UserVK::create([
                        'id' => $data['object']['user_id'],
                        'first_name' => $user['response'][0]['first_name'],
                        'last_name' => $user['response'][0]['last_name'],
                        'created_at' => date('Y-m-d H:i:s')
                    ])
                    ) {
                        $user = $res->get($data['object']['user_id']);
                        $res->addCommandEnd($user['id'], 'start');
                    } else {
                        $user = false;
                    }
                }
                if($user)
                {
                    $data['object']['body'] = trim($data['object']['body']);
                    $commandEnd = $res->getCommandEnd($user['id']);
                    if($commandEnd == 'start')
                    {
                        $text = "Привет {$user['first_name']} \xE2\x9C\x8C\nЧтобы мы могли понимать друг друга присылай мне команды \xF0\x9F\x98\xA4\nНапример сейчас ты можешь мне прислать цифру 1, тогда тебе будет доступен выбор факультета \xF0\x9F\x98\x9C\n\n";
                        $text .= (new Commands($user, 0))->executeCommandNumber();
                        $text .= "*********************\nУ ВК есть лимит на сообщения, чтобы не было проблем подпишись на меня \xE2\x9C\x8F (https://vk.com/rgsu_bot) \nСовсем скоро будут реализованы все функции, а так же СДО и многое другое =]\nTelegram - https://t.me/rgsu_bot";
                        $res->addCommandEnd($user['id'], false);
                    }
                    else if($commandEnd == 'select_faculty')
                    {
                        if($data['object']['body'] == 100) {
                            $text = (new Commands($user, $data['object']['body']))->executeCommandNumber();
                        } else {
                            $text = (new Commands($user, 'select_faculty', $data['object']['body']))->executeCommandText();
                        }
                    }
                    else if($commandEnd == 'select_group')
                    {
                        if($data['object']['body'] == 110) {
                            $text = (new Commands($user, $data['object']['body']))->executeCommandNumber();
                        } else {
                            $text = (new Commands($user, 'select_group', $data['object']['body']))->executeCommandText();
                        }
                    }
                    else if($commandEnd == 'select_call')
                    {
                        if($data['object']['body'] == 150) {
                            $text = (new Commands($user, $data['object']['body']))->executeCommandNumber();
                        } else {
                            $text = (new Commands($user, 'select_call', $data['object']['body']))->executeCommandText();
                        }
                    }
                    else
                    {
                        $text = (new Commands($user, $data['object']['body']))->executeCommandNumber();
                    }
                }
                else
                {
                    $text = "{$user['response'][0]['first_name']} 3(\nПроизошла ошибка.\nПопробуйте ещё раз =]";
                }
                Commands::sendMessage([
                    'message' => $text,
                    'user_id' => $user['id'],
                    'access_token' => env('VK_BOT_KEY'),
                    'v' => '5.0'
                ]);
                return 'ok';
                break;
        }
    }

}
