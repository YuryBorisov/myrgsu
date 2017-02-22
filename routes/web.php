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

Route::get('/', function () {
    //Faculty::truncate();
    //Faculty::create([
   //     'short_name' => 'ФУ',
     //   'full_name' => 'Факультет управления',
    //]);
});

Route::group(['prefix' => 'telegram'], function (){
    Route::match(['post', 'get'], '/', ['as' => 'telegram.message', 'uses' => 'TelegramController@message']);
    Route::group(['prefix' => 'hook'], function (){
        Route::get('/', ['as' => 'telegram.hook', 'uses' => 'TelegramController@hook']);
        Route::get('set', ['as' => 'telegram.hook.set', 'uses' => 'TelegramController@setWebhook']);
        Route::get('unset', ['as' => 'telegram.hook.unset', 'uses' => 'TelegramController@unsetWebhook']);
    });
});

Route::get('/parse', ['as' => 'parse', 'uses' => 'ParseController@parse']);

/*
 * $API_KEY = 'your_bot_api_key';
$BOT_NAME = 'namebot';
$hook_url = 'https://yourdomain/path/to/hook.php';
 */