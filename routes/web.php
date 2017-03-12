<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\Faculty;

Route::get('/ttt', function () {
    $str = "advertising platform";
    $arr = [];
    for ($i = 0; $i < 1000; $i++)
    {
        $st = '';
        for($j = 0; $j < 5; $j++)
        {
            $st .= $str[rand(0, strlen($str) - 1)];
        }
        $arr[] = $st;
    }
    header('Content-type: text/plaint');
    print_r($arr);
});

Route::group(['prefix' => 'telegram'], function (){
    Route::match(['post', 'get'], '334312989:AAEWEJVmWrh6XkNHKWdo_1waxE0r2G7eTjo', ['as' => 'telegram.message', 'uses' => 'TelegramController@message']);
    Route::group(['prefix' => 'hook'], function (){
        Route::get('/', ['as' => 'telegram.hook', 'uses' => 'TelegramController@hook']);
        Route::get('set', ['as' => 'telegram.hook.set', 'uses' => 'TelegramController@setWebhook']);
        Route::get('unset', ['as' => 'telegram.hook.unset', 'uses' => 'TelegramController@unsetWebhook']);
    });
});

//Route::get('/parse', ['as' => 'parse', 'uses' => 'ParseController@parse']);