<?php

/*
|--------------------------------------------------------------------------
| Patient routes
|--------------------------------------------------------------------------
|
| Routes for the pages accessible by the guest user (patient). That means
| workflow view and execution as well result review and export.
|
 */

Route::get('/', function () {
  return view('patient');
})->name('patient');

Route::get('/search', function () {
  return view('search');
});

Route::get('/workflow/{workflowId}', 'WorkflowController@index');
Route::post('/workflow/run', 'WorkflowController@runModel');

Route::get('/graph', 'GraphController@index');

Route::post('/PDF', function () {
  return view('PDF');
});

//Testing of rules engine
Route::get('/test-rules-engine', function () {
  return view('json-rules-engine-test');
});
