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

// Index Page
Route::resource('/home',"HomeController");

Route::post('/download',"HomeController@download");

// Setting Page @ CloudController
Route::resource('/cloud',"CloudController");
Route::get('/add',"CloudController@create");
Route::post('/add/{service}',"CloudController@add");
Route::get('/add/{service}',"CloudController@add");

Route::get("set",function(){
    Session::put("test","test");
    Session::save();
});

Route::get("get",function(){
    return Session::get("test");
});


Route::post('/ajax/post', function () {

    // pass back some data, along with the original data, just to prove it was received
    $data   = array('value' => 'some data', 'input' => Request::input());

    // return a JSON response
    return  Response::json($data);
});

// Route::get('/test', function(){
// 	$job = new App\Jobs\SendRemiderEmail;
// 	dispatch($job);

// 	return 'Done!';
// });
Route::get('/test', "PagesController@test");
Route::get('/context', "PagesController@context");
