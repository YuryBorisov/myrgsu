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

Route::get('/', function() {
    return 123;
});

Route::group(['prefix' => 'telegram'], function (){
    Route::match(['post', 'get'], '334312989:AAEWEJVmWrh6XkNHKWdo_1waxE0r2G7eTjo', ['as' => 'telegram.message', 'uses' => 'TelegramController@message']);
    Route::group(['prefix' => 'hook'], function (){
        Route::get('/', ['as' => 'telegram.hook', 'uses' => 'TelegramController@hook']);
        Route::get('set', ['as' => 'telegram.hook.set', 'uses' => 'TelegramController@setWebhook']);
        Route::get('unset', ['as' => 'telegram.hook.unset', 'uses' => 'TelegramController@unsetWebhook']);
    });
});
Route::any('vktest', ['as' => 'vk.index', 'uses' => 'VKController@index']);
Route::post('vk', ['as' => 'vk.index', 'uses' => 'VKController@index']);

//Route::get('/parse', ['as' => 'parse', 'uses' => 'ParseController@parse']);