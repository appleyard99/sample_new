<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
//简写
get('signup','UsersController@create')->name('signup');
//遵从RESTful架构为用户资源生成路由
resource('users','UsersController');

get('login','SessionsController@create')->name('login');
post('login','SessionsController@store')->name('login');
delete('logout','SessionsController@destory')->name('logout');
get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');


