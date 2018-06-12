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
})->name('patient');

Auth::routes();

Route::get('/emailverify/{token}', 'Auth\RegisterController@verifyUser')->name('emailverification');

Route::get('/usersverification', 'UsersVerificationController@index')->name('usersverification.index');

Route::get('/usersverification/download/{id}', 'UsersVerificationController@download')->name('usersverification.download');
Route::post('/usersverification/accept', 'UsersVerificationController@accept')->name('usersverification.accept');
Route::post('/usersverification/reject', 'UsersVerificationController@reject')->name('usersverification.reject');

Route::get("/editaccount", 'EditAccountController@index')->name('editaccount.index');
Route::post("/editaccount", 'EditAccountController@edit')->name('editaccount.edit');

Route::get('/notverified', function () {
  return view('notverified');
})->name('notverified')
  ->middleware('auth')
  ->middleware('can:not-view-designer');

Route::get('/designer', 'DesignerController@index')->name('designer')->middleware('auth');

Route::get('/graph', 'GraphController@index');

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

Route::get('/about', function () {
  return view('about');
})->name('about');

Route::get('/termsandconditions', function () {
  return view('termsandconditions');
})->name('termsandconditions');

Route::get('/privacypolicy', function () {
  return view('privacypolicy');
})->name('privacypolicy');

Route::get('/userguide',function() {
	return view('userguide');
})->name('userguide')->middleware('auth');

Route::get('/disclaimer',function() {
	return view('disclaimer');
})->name('disclaimer');

Route::get('/myworkflows', 'MyWorkflowsController@index')->name('myworkflows');
Route::get('/myworkflows/delete/{workflowId}', 'MyWorkflowsController@deleteWorkflow');
Route::get('/myworkflows/publish/{workflowId}', 'MyWorkflowsController@publishWorkflow');

//Testing of rules engine
Route::get('/test-rules-engine', function () {
  return view('json-rules-engine-test');
});
