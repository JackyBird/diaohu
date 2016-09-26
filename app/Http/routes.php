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

function user_ins() {
    return new App\User;
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

Route::any('test', function () {
    dd(user_ins()->is_logged_in());
});