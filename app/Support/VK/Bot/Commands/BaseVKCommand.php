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

    const CHAT_ROOM_EXIT = 101;

    const CHAT_EXIT = self::CANCELED;

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

    const PERSONAL_AREA_SDO_VIEW = [
        'class' => PersonalAreaCommand::class,
        'method' => 'sdoView'
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

    const PERSONAL_BROADCASTING_SELECT_LOCATION = [
        'class' => PersonalAreaCommand::class,
        'method' => 'selectLocationBroadcasting'
    ];

    const MAIN_MENU_ROOM_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'roomView'
    ];

    const MAIN_MENU_ROOM_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectRoom'
    ];

    const MAIN_MENU_TEACHERS_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'teachersView'
    ];

    const MAIN_MENU_TEACHERS_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectTeachers'
    ];

    const MAIN_MENU_NEWS_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'newsView'
    ];

    const MAIN_MENU_PLAYER_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'playerView'
    ];

    const MAIN_MENU_DISTRIBUTION_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'distributionView'
    ];

    const MAIN_MENU_DISTRIBUTION_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectDistribution'
    ];

    const MAIN_MENU_WISHES_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'wishesView'
    ];

    const MAIN_MENU_FEEDBACK_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'feedbackView'
    ];

    const MAIN_MENU_CHAT_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'chatView'
    ];

    const MAIN_MENU_CHAT_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectChat'
    ];

    const MAIN_MENU_CHAT_MESSAGE = [
        'class' => MainMenuCommand::class,
        'method' => 'messageChat'
    ];

    const MAIN_MENU_NOTIFICATIONS_VIEW = [
        'class' => MainMenuCommand::class,
        'method' => 'notificationsView'
    ];

    const MAIN_MENU_NOTIFICATIONS_SELECT = [
        'class' => MainMenuCommand::class,
        'method' => 'selectNotifications'
    ];

    const MAIN_MENU_NOTIFICATIONS_SELECT_USER = [
        'class' => MainMenuCommand::class,
        'method' => 'selectNotificationsUser'
    ];

    public abstract function mainSelect();

}