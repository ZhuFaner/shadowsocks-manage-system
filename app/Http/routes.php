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

Route::auth();


Route::get('/', 'HomeController@index');

Route::get('rank','HomeController@show_rank');
Route::get('user_manage','HomeController@show_member');
Route::get('user_detail','HomeController@user_detail');
Route::get('edit_user','HomeController@user_edit');
Route::post('hour_flow','HomeController@hour_flow');
Route::post('day_flow','HomeController@day_flow');
Route::post('week_flow','HomeController@week_flow');
Route::post('del_user','HomeController@delete_member');
Route::post('update_user','HomeController@update_member');
Route::post('add','HomeController@add_user');

Route::get('add_user',function (){
    return view('add_user');
});

Route::group(['middleware' => 'auth', 'prefix' => 'node'], function ()
{
  Route::get('list', 'NodeController@index');
  Route::get('create', 'NodeController@create');
  Route::post('store', 'NodeController@store');
  Route::get('edit/{id}', 'NodeController@edit');
  Route::post('update/{id}', 'NodeController@update');
  Route::post('delete/{id}', 'NodeController@delete');
});

// Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function() {
//     Route::get('/', 'HomeController@index');
// });

