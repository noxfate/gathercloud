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

Route::get('/', "PagesController@index");

Route::get('/login',"UserController@index");
Route::post('/login', "UserController@authenticate");
Route::get('/logout', "UserController@logout");
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store");

Route::resource('/home',"HomeController");
Route::get('/add',"CloudController@index");
//Route::post('/add/{service}',"CloudController@add");
Route::get('/add/{service}',"CloudController@add");
