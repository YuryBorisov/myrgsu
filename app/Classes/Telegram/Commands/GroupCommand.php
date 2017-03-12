<?php
namespace App\Classes\Telegram\Commands;

use App\Repositories\FacultyRepository;
use App\Repositories\UserTelegramRepository;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class GroupCommand extends MyCommand
{

    protected $prefix;

    public function __construct(Telegram $telegram, Update $update, $prefix)
    {
        parent::__construct($telegram, $update);
        $this->prefix = $prefix;
    }

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $userTelegram = UserTelegramRepository::instance()->get($this->chatId);
        $groupsButton = [];
        if($this->prefix == ConstantCommand::MY)
        {
            if($userTelegram['faculty_id'])
            {
                $groups = FacultyRepository::instance()->get($userTelegram['faculty_id'])['groups'];
                $groupsButton = [
                    [
                        ['text' => 'Назад', 'callback_data' => 'my_schedule'],
                        ['text' => 'Главное меню', 'callback_data' => 'main_menu']
                    ]
                ];
                for($i = 0, $j = count($groups); $i < $j; $i++)
                {
                    $groupsButton[$i + 1][] = [
                        'text' => $groups[$i]['short_name'], 'callback_data' => $this->prefix . '_group_select_' . $groups[$i]['id']
                    ];
                }
            }
            else
            {
                return (new ErrorCommand($this->telegram, $this->update ,$this->update->getCallbackQuery()->getMessage()->getChat()->getFirstName() .
                    ' сначала выбери факультет ' . "\xF0\x9F\x98\xA1"))->execute();
            }
            Request::editMessageText([
                'chat_id' => $this->chatId,
                'text' => 'Группы',
                'reply_markup' => (new \ReflectionClass(InlineKeyboard::class))->newInstanceArgs($groupsButton),
                'message_id' => $this->messageId
            ]);
        }
    }
}