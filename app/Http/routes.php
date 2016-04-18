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

// Authentication System
Route::get('/login',"UserController@login");
Route::post('/login', "UserController@authenticate");
Route::get('/logout', "UserController@logout");
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store");

// Index Page
Route::get('/home/search', "HomeController@search");
Route::post('/home/download',"HomeController@download");
Route::get('/home/download',"HomeController@download");
Route::post('/home/upload', 'HomeController@upload');
Route::post('/home/delete', 'HomeController@delete');
Route::post('/home/rename', 'HomeController@rename');
Route::resource('/home',"HomeController");

// Setting
Route::resource('/setting/cloud',"CloudController");
Route::resource('/setting/profile', "UserController");

// Add Cloud Connection Route
Route::get('/add',"CloudController@create");
Route::post('/add/{service}',"CloudController@add");
Route::get('/add/{service}',"CloudController@add");

// Redundancy Checking Ver.1
Route::get('/upload',function(){
    return view('pages.upload-temp');
});

// GatherLink Ver. 1
Route::get('/gtl/shared', 'GatherlinkController@showFromToken');
Route::get('/gtl/select', 'GatherlinkController@select');
Route::resource('/gtl','GatherlinkController');



// =========== FOR TESTING ==================
Route::post('/ajax/post', function () {
    // pass back some data, along with the original data, just to prove it was received
    $data   = array('value' => 'some data', 'input' => Request::input());

    // return a JSON response
    return  Response::json($data);
});

Route::get('/job', function(){
    $job = (new \App\Jobs\CreateFileMapping($_GET['name']));
    dispatch($job);
});

Route::get('/test', 'HomeController@test');

Route::get("set",function(){
    Session::put("test","test");
    Session::save();
});

Route::get("get",function(){
    return Session::get("test");
});

// ===========================================