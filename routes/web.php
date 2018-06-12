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

Route::get('/emailverify/{token}','Auth\RegisterController@verifyUser')->name('emailverification');

Route::get('/usersverification', 'UsersVerificationController@index')->name('usersverification.index');

Route::get('/usersverification/download/{id}', 'UsersVerificationController@download')->name('usersverification.download');
Route::post('/usersverification/accept','UsersVerificationController@accept')->name('usersverification.accept');
Route::post('/usersverification/reject','UsersVerificationController@reject')->name('usersverification.reject');

Route::get('/notverified', function() {
  return view('notverified');
})->name('notverified')
->middleware('auth')
->middleware('can:not-view-designer');

Route::get('/designer', 'DesignerController@index')->name('designer')->middleware('auth');

Route::post('/graph', 'GraphController@index');

Route::get('/search', function () {
  return view('search');
});
Route::get('/workflow/{workflowId}', 'WorkflowController@index');
Route::post('/workflow/run', 'WorkflowController@runModel');

Route::post('/PDF', function () {
  return view('PDF');
});

Route::post('/designer/fetch', 'DesignerController@fetchVariables');
Route::post('/designer/runmodel', 'DesignerController@runModel');
Route::post('/designer/search', 'DesignerController@fetchSearch');


Route::post('/designer/save', 'DesignerSaveController@saveWorkflow');
Route::post('/designer/save/{workflowId}', 'DesignerSaveController@saveWorkflow');
Route::post('/designer/load/{workflowId}', 'DesignerLoadController@loadWorkflow');

Route::get('/myworkflows', 'MyWorkflowsController@index')->name('myworkflows');
Route::get('/myworkflows/delete/{workflowId}', 'MyWorkflowsController@deleteWorkflow');

//Testing of rules engine
Route::get('/test-rules-engine', function () {
  return view('json-rules-engine-test');
});
