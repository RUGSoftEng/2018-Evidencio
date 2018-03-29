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

Route::get('/', function () {
    return view('patient');
});

Auth::routes();

Route::get('/designer', 'DesignerController@index')->name('designer');

Route::get('/search',function(){
  return view('search');
});

Route::get('/workflow',function(){
  return view('workflow');
});

Route::get('/graph',function(){
  return view('graph');
});

Route::post('/graph',function(){
  return view('graph');
});
