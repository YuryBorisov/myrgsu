<?php

namespace App\Classes\Telegram\Commands;

use App\Repositories\FacultyRepository;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class FacultyCommand extends MyCommand
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
        $faculties = [
            [
                ['text' => 'Назад', 'callback_data' => 'my_schedule'],
                ['text' => 'Главное меню', 'callback_data' => 'main_menu']
            ]
        ];
        for($facultiesR = FacultyRepository::instance()->all(), $i = 0, $j = count($facultiesR), $k = 0; $i < $j; $i++, $k++)
        {
            $faculties[$i + 1][$k] = ['text' => $facultiesR[$i]['short_name'], 'callback_data' => $this->prefix . '_faculty_select_' . $facultiesR[$i]['id']];
        }
        $inlineKeyboard = (new \ReflectionClass(InlineKeyboard::class))->newInstanceArgs($faculties);
        $data = [
            'chat_id'      => $this->chatId,
            'text'         => 'Факультеты',
            'reply_markup' => $inlineKeyboard,
            'message_id' => $this->messageId
        ];
        return Request::editMessageText($data);
    }
}