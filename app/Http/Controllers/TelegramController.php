<?php

namespace App\Http\Controllers;

use App\Classes\Telegram\Commands\CallCommand;
use App\Classes\Telegram\Commands\ConstantCommand;
use App\Classes\Telegram\Commands\FacultyCommand;
use App\Classes\Telegram\Commands\FeedbackCommand;
use App\Classes\Telegram\Commands\GroupCommand;
use App\Classes\Telegram\Commands\MainMenuCommand;
use App\Classes\Telegram\Commands\ScheduleCommand;
use App\Classes\Telegram\Commands\SelectCommand;
use App\Classes\Telegram\Commands\SettingsCommand;
use App\Classes\Telegram\Commands\StartCommand;
use App\Classes\Telegram\Commands\TeachersCommand;
use App\Models\UserTelegram;
use App\Repositories\UserTelegramRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class TelegramController extends Controller
{

    public function setWebhook()
    {
        try
        {
            $telegram = new Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_NAME'));
            $result = $telegram->setWebhook(env('TELEGRAM_BOT_HOOK_URL'));

            if ($result->isOk()) {
                echo $result->getDescription();
            }
        }
        catch (\Throwable $e)
        {
            return $e->getMessage();
        }
    }

    public function hook()
    {
        try
        {
            $telegram = new Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_NAME'));
            $telegram->handle();
        }
        catch (\Throwable $e)
        {
            return $e->getMessage();
        }
    }

    public function unsetWebhook()
    {
        try {
            $telegram = new Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_NAME'));
            $result = $telegram->deleteWebhook();
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }

    public function message(Request $request)
    {
        try
        {
            $telegram = new Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_NAME'));
            $update = new Update($request->all());
            switch ($update->getUpdateType())
            {
                case ConstantCommand::MESSAGE:
                    $command = null;
                    switch ($update->getMessage()->getCommand())
                    {
                        case 'start':
                            if(!UserTelegramRepository::instance()->get($chatId = $update->getMessage()->getChat()->getId()))
                            {
                               UserTelegram::create(['id' => $chatId]);
                            }
                            $start = new StartCommand($telegram, $update);
                            $start->execute();
                            $mainMenuCommand = new MainMenuCommand($telegram, $update);
                            $mainMenuCommand->execute();
                            break;
                        case 'menu':
                            $mainMenuCommand = new MainMenuCommand($telegram, $update);
                            $mainMenuCommand->execute();
                            break;
                    }
                    break;
                case ConstantCommand::CALLBACK_QUERY:
                    $command = null;
                    $arr = explode('_', $update->getCallbackQuery()->getData());
                    switch ($arr[0])
                    {
                        case 'main':
                            switch ($arr[1])
                            {
                                case 'menu':
                                    $command = new MainMenuCommand($telegram, $update);
                                    break;
                            }
                            break;
                        case 'my':
                            switch ($arr[1])
                            {
                                case 'schedule':
                                    if(isset($arr[2]))
                                    {
                                        switch ($arr[2])
                                        {
                                            case ConstantCommand::MY_SCHEDULE_TODAY:
                                                $command = new ScheduleCommand($telegram, $update, ConstantCommand::MY_SCHEDULE_TODAY);
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $command = new ScheduleCommand($telegram, $update, ConstantCommand::MY);
                                    }
                                    break;
                                case 'faculty':
                                    if(isset($arr[2]))
                                    {
                                        switch ($arr[2])
                                        {
                                            case 'select':
                                                $command = new SelectCommand($telegram, $update, ConstantCommand::SELECT_MY_SCHEDULE_FACULTY, $arr[3]);
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $command = new FacultyCommand($telegram, $update, ConstantCommand::MY);
                                    }
                                    break;
                                case 'group':
                                    if(isset($arr[2]))
                                    {
                                        switch ($arr[2])
                                        {
                                            case 'select':
                                                $command = new SelectCommand($telegram, $update, ConstantCommand::SELECT_MY_SCHEDULE_GROUP, $arr[3]);
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $command = new GroupCommand($telegram, $update, ConstantCommand::MY);
                                    }
                                    break;
                            }
                            break;
                        case 'schedule':
                            break;
                        case 'settings':
                            $command = new SettingsCommand($telegram, $update);
                            break;
                        case 'teachers':
                            $command = new TeachersCommand($telegram, $update);
                            break;
                        case 'feedback':
                            $command = new FeedbackCommand($telegram, $update);
                            break;
                        case 'call':
                            switch ($arr[1])
                            {
                                case 'schedule':
                                    $command = new CallCommand($telegram, $update);
                                    break;
                            }
                            break;
                    }
                    $command->execute();
                    break;
            }
        }
        catch (TelegramException $e)
        {
            Log::info($e->getMessage());
        }
    }

}