<?php

/*
|--------------------------------------------------------------------------
| Designer routes
|--------------------------------------------------------------------------
|
| Routes for the workflow designer page and any AJAX calls made in it.
|
 */

Route::get('/designer', 'DesignerController@index')->name('designer');
Route::post('/designer/fetch', 'DesignerController@fetchVariables');
Route::post('/designer/runmodel', 'DesignerController@runModel');
Route::post('/designer/search', 'DesignerController@fetchSearch');


Route::post('/designer/save', 'DesignerSaveController@saveWorkflow');
Route::post('/designer/save/{workflowId}', 'DesignerSaveController@saveWorkflow');
Route::post('/designer/load/{workflowId}', 'DesignerLoadController@loadWorkflow');
