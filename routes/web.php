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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');


Route::get('signup', 'UsersController@create')->name('signup');
Route::resource('users', 'UsersController');

// 会话路由
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

// 邮箱验证路由
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// 微博相关路由
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 显示用户的关注人列表路由
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
// 显示用户的粉丝列表路由
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

// 关注用户理由
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
// 取消关注用户路由
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');