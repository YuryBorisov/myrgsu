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
        foreach (UserVK::where(['distribution' => 0])->get() as $user)
        {
            if($i == 3)
            {
                sleep(5);
                $i = 0;
            }
            Commands::sendMessage([
                'message' => "Привет {$user->first_name} \xE2\x9C\x8C\nКоманда '\xF0\x9F\x93\xB0 Новости РГСУ' перестала работать [Заработает в ближайшее время]",
                'user_id' => $user['id'],
                'access_token' => env('VK_BOT_KEY'),
                'v' => '5.0'
            ]);
            echo $user->id."\n";
            $i++;
        }
    }
}
