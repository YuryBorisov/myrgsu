<?php

namespace App\Support\VK\Bot\Commands;

use App\Support\Base\BaseCommand;

abstract class BaseVKCommand extends BaseCommand
{

    const SEPARATOR = '*******************';

    const MAIN_SELECT = 'mainSelect';

    const VIEW = 'view';

    const NOT_COMMAND = "Нет такой команды [Пришли мне цифру]\n".self::SEPARATOR."\n";

    const CANCELED = 100;

    const YES = 1;

    const MAIN_MENU_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => self::VIEW
    ];

    const WELCOME_VIEW = [
        'class' => WelcomeCommand::class,
        'method' => self::VIEW
    ];

    const MAIN_MENU_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => self::MAIN_SELECT
    ];

    const PERSONAL_AREA_VIEW = [
        'class' => PersonalAreaCommand::class,
        'method' =>self::VIEW
    ];

    const PERSONAL_AREA_SELECT = [
        'class' => PersonalAreaCommand::class,
        'method' => self::MAIN_SELECT
    ];

    const SCHEDULE_VIEW = [
        'class' => ScheduleCommand::class,
        'method' => self::VIEW
    ];

    const SCHEDULE_SELECT = [
        'class' => ScheduleCommand::class,
        'method' => self::MAIN_SELECT
    ];

    const SCHEDULE_FACULTY_VIEW = [
        'class' => ScheduleCommand::class,
        'method' => 'facultyView'
    ];

    const SCHEDULE_FACULTY_SELECT = [
        'class' => ScheduleCommand::class,
        'method' => 'selectFaculty'
    ];

    const SCHEDULE_GROUP_VIEW = [
        'class' => ScheduleCommand::class,
        'method' => 'groupView'
    ];

    const SCHEDULE_GROUP_SELECT = [
        'class' => ScheduleCommand::class,
        'method' => 'selectGroup'
    ];

    const SCHEDULE_TODAY = [
        'class' => ScheduleCommand::class,
        'method' => 'today'
    ];

    const SCHEDULE_TOMORROW_DAY = [
        'class' => ScheduleCommand::class,
        'method' => 'tomorrowDay'
    ];

    const SCHEDULE_WEEK = [
        'class' => ScheduleCommand::class,
        'method' => 'week'
    ];

    const SCHEDULE_CALL_VIEW = [
        'class' => ScheduleCommand::class,
        'method' => 'call'
    ];

    const SCHEDULE_CALL_SELECT = [
        'class' => ScheduleCommand::class,
        'method' => 'selectCall'
    ];

    const PERSONAL_AREA_LOCATION_VIEW = [
        'class' => PersonalAreaCommand::class,
        'method' => 'locationView'
    ];

    const PERSONAL_AREA_LOCATION_SELECT = [
        'class' => PersonalAreaCommand::class,
        'method' => 'selectLocation'
    ];

    const PERSONAL_BROADCASTING_VIEW = [
        'class' => PersonalAreaCommand::class,
        'method' => 'broadcastingView'
    ];

    const PERSONAL_BROADCASTING_SELECT = [
        'class' => PersonalAreaCommand::class,
        'method' => 'selectBroadcasting'
    ];

    const MAIN_MENU_ROOM_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'roomView'
    ];

    const MAIN_MENU_ROOM_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectRoom'
    ];

    public abstract function mainSelect();

}