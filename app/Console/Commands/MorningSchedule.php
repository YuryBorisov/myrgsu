<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\User;
use App\Support\VK\Bot\Commands\PersonalAreaCommand;
use App\Support\VK\Bot\Commands\ScheduleCommand;
use App\Support\VK\Bot\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        foreach (DB::table('users')->where([['call', 0], ['service_id', Service::VK], ['group_id', '!=', 0]])->get() as $user)
        {
            if($i == 3)
            {
                sleep(5);
                $i = 0;
            }
            $user = (array) $user;
            $s = new ScheduleCommand($user, null, null);
            Request::sendMessage([
                'user_id' => $user['user_id'],
                'message' => "Доброе утро {$user['first_name']}.\n\n".$s->today(false)
            ]);
            $i++;
        }
    }
}
