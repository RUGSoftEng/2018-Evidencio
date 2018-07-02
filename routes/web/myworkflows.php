<?php

/*
|--------------------------------------------------------------------------
| "My Workflows" Routes
|--------------------------------------------------------------------------
|
| Routes for the "My Workflows" page and its AJAX requests.
|
 */

Route::get('/myworkflows', 'MyWorkflowsController@index')->name('myworkflows');
Route::get('/myworkflows/delete/{workflowId}', 'MyWorkflowsController@deleteWorkflow');
Route::get('/myworkflows/publish/{workflowId}', 'MyWorkflowsController@publishWorkflow');
