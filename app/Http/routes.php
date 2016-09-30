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

function rq($key = null, $default = null)
{
    if (!$key)
        return Request::all();
    return Request::get($key, $default);
}

function user_ins()
{
    return new App\User;
}

function question_ins()
{
    return new App\Question;
}

Route::get('/', function () {
    return view('welcome');
});

Route::any('api', function () {
    return 'version';
});

Route::any('api/signup', function () {
    return user_ins()->signup();
});

Route::any('api/login', function () {
    return user_ins()->login();
});

Route::any('api/logout', function () {
    return user_ins()->logout();
});

Route::any('api/question/add', function () {
    return question_ins()->add();
});

Route::any('api/question/change', function () {
    return question_ins()->change();
});

Route::any('api/question/read', function () {
    return question_ins()->read();
});

Route::any('api/question/remove', function () {
    return question_ins()->remove();
});

Route::any('test', function () {
    dd(user_ins()->is_logged_in());
});