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


Route::get('/myworkflows', function () {
    return view('myworkflows');
});

Auth::routes();

Route::get('/designer', 'DesignerController@index')->name('designer');

Route::post('/graph', 'GraphController@index');



Route::get('/search',function(){
  return view('search');
});

Route::post('/graph',function(){
  return view('graph');
});

Route::get('/designer/fetch', 'DesignerController@fetchVariables');
