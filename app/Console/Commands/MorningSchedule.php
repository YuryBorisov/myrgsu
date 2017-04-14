<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\UserVK;
use Illuminate\Console\Command;

class MorningSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'morning_schedule';

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
            $text = false;
            if($user->group_id != 0)
            {
                $text = (new Commands($user, 12))->executeCommandNumber();
            }
            if($text)
            {
                if($i == 3)
                {
                    sleep(5);
                    $i = 0;
                }
                Commands::sendMessage([
                    'message' => "Привет {$user->first_name} \xE2\x9C\x8C [Уведомление]\n\n". $text,
                    'user_id' => $user['id'],
                    'access_token' => env('VK_BOT_KEY'),
                    'v' => '5.0'
                ]);
                $i++;
            }
        }
    }
}
