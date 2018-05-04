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

Route::get('/designer', 'DesignerController@index')->name('designer')->middleware('auth');

Route::post('/graph', 'GraphController@index');

Route::get('/search', function () {
  return view('search');
});

Route::get('/workflow', function () {
  return view('workflow');
});


Route::post('/designer/fetch', 'DesignerController@fetchVariables')->middleware('auth');

Route::post('/designer/save', 'DesignerController@saveWorkflow')->middleware('auth');
Route::post('/designer/save/{workflowId}', 'DesignerController@saveWorkflow')->middleware('auth');