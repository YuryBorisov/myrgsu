<?php

namespace App\Console\Commands;

use App\Classes\VK\Commands\Commands;
use App\Models\User;
use App\Models\UserVK;
use App\Repositories\GroupRepository;
use App\Support\VK\Bot\Request;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;
        $dayOfWeek = Carbon::now()->dayOfWeek;
        $count = 0;
        if($dayOfWeek == 6)
        {
            $dayOfWeek = 0;
        }
        else
        {
            $dayOfWeek++;
        }
        foreach (User::where(['call' => 0])->get() as $user)
        {
            if ($user->group_id == 0) {
                $text = "Привет {$user->first_name} \xE2\x9C\x8C [Уведомление]\nВыбери свою группу, чтобы я присылал тебе каждый день уведомления о завтрашних занятиях\n\nЕсли ты не хочешь получать данное уведомление просто отключи его в разделе \"7. \xF0\x9F\x93\xA2 Уведомления\"";
            } else {
                if(count(GroupRepository::instance()->getActiveSubjectDay($user->group_id, $dayOfWeek)) >= 1) {
                    $text = (new Commands($user, 13))->executeCommandNumber();
                    $text = "[Уведомление]\n" . $text;
                } else {
                    $text = "Привет {$user->first_name} \xE2\x9C\x8C [Уведомление]\n\nЗавтра у тебя нет пар \xF0\x9F\x98\xB1";
                }
            }

            Request::sendMessage([
                'message' => $text,
                'user_id' => $user->user_id,
            ]);
            $output->writeln(['<info>Отправили: ' . $user->first_name . ' ' . $user->last_name . '</info>']);
            if($i == 8)
            {
                sleep(4);
                $i = 0;
            }
            $i++;
            $count++;
        }
        $output->writeln([
           '<info>Всего отправили ' . $count . ' пользователям</info>'
        ]);
    }
}
