<?php

/*
|--------------------------------------------------------------------------
| User Verification Routes
|--------------------------------------------------------------------------
|
| Routes for the "User Verification" page and its requests.
|
 */

Route::get('/usersverification', 'UsersVerificationController@index')->name('usersverification.index');

Route::get('/usersverification/download/{id}', 'UsersVerificationController@download')->name('usersverification.download');
Route::post('/usersverification/accept', 'UsersVerificationController@accept')->name('usersverification.accept');
Route::post('/usersverification/reject', 'UsersVerificationController@reject')->name('usersverification.reject');
