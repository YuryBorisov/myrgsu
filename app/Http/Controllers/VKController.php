<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Support\VK\Bot\Commands\BaseVKCommand;
use App\Support\VK\Bot\Manager;
use App\Support\VK\Bot\Request;
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
                if(!$user = ($repository = UserRepository::instance())->get($data['object']['user_id'], Service::VK))
                {
                    $user = json_decode(Request::getUser([
                        'user_ids' => $data['object']['user_id'],
                        'access_token' => env('VK_BOT_KEY'),
                        'v' => 8.84
                    ]), true);
                    $user = User::create([
                        'service_id' => Service::VK,
                        'user_id' => $data['object']['user_id'],
                        'first_name' => $user['response'][0]['first_name'],
                        'last_name' => $user['response'][0]['last_name'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $user = $repository->addCommand($user['user_id'], Service::VK, BaseVKCommand::WELCOME_VIEW);
                }
                if(strlen($text = (new Manager($user, $data))->run()) > 0)
                {
                    Request::sendMessage([
                        'user_id' => $user['user_id'],
                        'message' => $text
                    ]);
                }
                echo("ok");
                break;
        }

    }

}