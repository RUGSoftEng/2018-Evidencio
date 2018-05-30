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

Route::get('/usersverification', 'UsersVerificationController@index')->name('usersverification.index');
Route::post('/usersverification/accept', 'UsersVerificationController@accept')->name('usersverification.accept');
Route::post('/usersverification/reject', 'UsersVerificationController@reject')->name('usersverification.reject');

Route::get('/designer', 'DesignerController@index')->name('designer')->middleware('auth');

Route::post('/graph', 'GraphController@index');

Route::get('/search', function () {
  return view('search');
});
Route::get('/workflow/{workflowId}', 'WorkflowController@index');

Route::post('/PDF', function () {
  return view('PDF');
});

Route::post('/designer/fetch', 'DesignerController@fetchVariables')->middleware('auth');
Route::post('/designer/runmodel', 'DesignerController@runModel')->middleware('auth');
Route::post('/designer/search', 'DesignerController@fetchSearch')->middleware('auth');


Route::post('/designer/save', 'DesignerSaveController@saveWorkflow')->middleware('auth');
Route::post('/designer/save/{workflowId}', 'DesignerSaveController@saveWorkflow')->middleware('auth');
Route::post('/designer/load/{workflowId}', 'DesignerLoadController@loadWorkflow')->middleware('auth');

Route::get('/myworkflows','MyWorkflowsController@index')->name('myworkflows')->middleware('auth');
Route::get('/myworkflows/delete/{workflowId}','MyWorkflowsController@deleteWorkflow')->middleware('auth');