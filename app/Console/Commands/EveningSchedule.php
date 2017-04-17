<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\UserVK;
use App\Repositories\GroupRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EveningSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evening_schedule';

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
        $dayOfWeek = Carbon::now()->dayOfWeek;
        if($dayOfWeek == 6)
        {
            $dayOfWeek = 0;
        }
        else
        {
            $dayOfWeek++;
        }
        foreach (UserVK::where(['call' => 0])->get() as $user)
        {
            $text = false;
            if($user->call == 0)
            {
                if($user->group_id != 0)
                {
                    if(count(GroupRepository::instance()->getActiveSubjectDay($user->group_id, $dayOfWeek)) > 0) { 
                        $text = (new Commands($user, 13))->executeCommandNumber();
                    } else {
                        $text = "Привет {$user->first_name} \xE2\x9C\x8C [Уведомление]\n\nЗавтра у тебя нет пар \xF0\x9F\x98\xB1\nРазвлекайся \xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89 \xF0\x9F\x8E\x89";
                    }
                }
            }
            if($text)
            {
                if($i == 5)
                {
                    sleep(5);
                    $i = 0;
                }
                Commands::sendMessage([
                    'message' => $text,
                    'user_id' => $user->id,
                    'access_token' => env('VK_BOT_KEY'),
                    'v' => '5.0'
                ]);
                $i++;
            }
        }
    }
}
