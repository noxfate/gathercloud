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

Route::get('/', "HomeController@index");

Route::post('/login', "UserController@authenticate");
Route::get('/logout', "UserController@logout");
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store");

Route::get('/home', function(){
    if (Auth::check())
        return response()->view("home");
    else
        return response()->view("index");
});

Route::get('/home/add/{service}', 'CloudController@add');


Route::get('/dropbox', 'HomeController@dropbox');

//Route::get('/copy','HomeController@copy');

//Route::get('/copy/getAccessToken', 'HomeController@copy');


