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

Route::get('/test', 'HomeController@test');



Route::get('/', "PagesController@index");

// Authentication System
Route::get('/login',"UserController@login");
Route::post('/login', "UserController@authenticate");
Route::get('/logout', "UserController@logout");
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store");

// Index Page
Route::get('/home/{id}', 'HomeController@index');
Route::get('/home/{id}/{any}', 'HomeController@show')->where('any', '.*');
Route::get('/search/{id}', "HomeController@search");
Route::get('/download',"HomeController@download");
Route::post('/upload', array(
    'as' => 'upload',
    'uses' => 'HomeController@upload'));
Route::post('/upload-dummy', array(
    'as'    =>  'upload-dummy',
    'uses'  =>  'HomeController@upload_dummy'));
Route::any('/getStorages', array(
    'as'    =>  'getStorages',
    'uses'  =>  'HomeController@getStorages'));
Route::any('/checkStorage', array(
    'as'    =>  'checkStorage',
    'uses'  =>  'HomeController@checkStorage'));
Route::any('/redundancyCheck', array(
    'as'    =>  'redundancyCheck',
    'uses'  =>  'HomeController@redundancyCheck'));
Route::any('/getFolderList', array(
    'as'    =>  'getFolderList',
    'uses'  =>  'HomeController@getFolderList'));
Route::any('/getConnectionList', array(
    'as'    =>  'getConnectionList',
    'uses'  =>  'HomeController@getConnectionList'));
Route::any('/transferFile', array(
    'as'    =>  'transferFile',
    'uses'  =>  'HomeController@transferFile'));
Route::post('/createFolder', array(
    'as'    => 'createFolder',
    'uses'  =>  'HomeController@createFolder'));
Route::post('/{any}/delete', 'HomeController@delete')->where('any', '.*');
Route::post('/rename', array(
    'as'    => 'rename',
    'uses'  =>  'HomeController@rename'));
Route::any('/getLink', array(
    'as'    => 'getLink',
    'uses'  =>  'HomeController@getLink'));
Route::any('/copy', array(
    'as'    => 'copy',
    'uses'  =>  'HomeController@copy'));
Route::any('/move', array(
    'as'    => 'move',
    'uses'  =>  'HomeController@move'));
Route::get('/home',function ()
{
    if (!Auth::check())
        return view('pages.info.login');
    return Redirect::to('/home/all');
});

//===========================================================

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
Route::any('/gtl/select/all', array(
    'as' => 'select',
    'uses' => 'GatherlinkController@select'));
Route::any('/gtl/select', array(
    'as' => 'selectIn',
    'uses' => 'GatherlinkController@selectIn'));
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

Route::get("set",function(){
    Session::put("test","test");
    Session::save();
});

Route::get("get",function(){
    return Session::get("test");
});

// ===========================================