<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\UserVK;
use Illuminate\Console\Command;

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
        $i = 0;
        foreach (UserVK::where(['call' => 0])->get() as $user)
        {
            if($i == 10)
            {
                sleep(5);
                $i = 0;
            }
            Commands::sendMessage([
                'message' => "Привет {$user->first_name} \xE2\x9C\x8C \n\nТеперь тебе вечером и с утра будут приходить уведомления [расписание]\nТак же ты можешь их отключить и заново включить =] (В разделе 'Моё расписание' добавлена новая команда, посмотри)\nХорошей ночи.",
                'user_id' => $user['id'],
                'access_token' => env('VK_BOT_KEY'),
                'v' => '5.0'
            ]);
            $i++;
        }
    }
}
