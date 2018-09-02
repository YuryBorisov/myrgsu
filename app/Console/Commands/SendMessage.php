<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\User;
use App\Models\UserVK;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;
        $sleep = 4;
        foreach (User::all() as $user)
        {
            if($i == 4)
            {
                $output->writeln([
                   '<info>Sleep to ' . $sleep . ' seconds</info>'
                ]);
                sleep($sleep);
                $i = 0;
            }
            Commands::sendMessage([
                'message' => "Привет {$user->first_name} \xE2\x9C\x8C\nС началом учебного года \xF0\x9F\x8E\x93 \xF0\x9F\x8E\x89\nДумаю, что ты меня ещё не забыл \xF0\x9F\x92\xAC\nВыбирай свою группу и получай расписание в считанные секунды \xF0\x9F\x9A\x80\nРассказывай обо мне своим одногруппникам и всем друзьям из университета \xF0\x9F\x93\xA2\nУдачи в этом учебном году \xF0\x9F\x98\x89 \xF0\x9F\x98\x89",
                'user_id' => $user['user_id'],
                'access_token' => env('VK_BOT_KEY'),
                'v' => '5.84'
            ]);
            $output->writeln([
                '<info>Send to: ' . $user->first_name . ' ' . $user->last_name . ' [' . $user->user_id . ']'
            ]);
            $i++;
        }
    }

}