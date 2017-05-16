<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\User;
use App\Models\UserVK;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (User::all() as $user)
        {
            $arr = [
                'schedule' => $user->call,
                'broadcasting' => 0,
                'news_project' => $user->distribution
            ];
            DB::table('users')->where('id', $user->id)->update(['notifications' => json_encode($arr)]);
        }
        /*
        $i = 0;
        foreach (User::whereDistribution(0)->get() as $user)
        {

            if($i == 3)
            {
                sleep(5);
                $i = 0;
            }
            Commands::sendMessage([
                'message' => "Доброе утро {$user->first_name} \xE2\x9C\x8C\nБыл добавлен личный кабинет, теперь там '\xF0\x9F\x93\x85 Расписание' и ещё кое - что \xF0\x9F\x98\x89\nКоманды '\xF0\x9F\x9A\xAA Аудитории', '\xF0\x9F\x91\x94 Преподаватели', '\xF0\x9F\x93\xB0 Новости РГСУ' теперь работают.\n\xF0\x9F\x8E\xBA Вещание - новая команда в личном кабинете. Отправляй сообщения всем кто учиться на твоем факультете и не только.\nХорошего дня =]\n",
                'user_id' => $user['user_id'],
                'access_token' => env('VK_BOT_KEY'),
                'v' => '5.0'
            ]);
            echo $user->user_id."\n";
            $i++;
        }
        */
    }

}