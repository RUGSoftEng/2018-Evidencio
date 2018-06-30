<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Routes for authentication and authorisation pages.
|
 */

Auth::routes();

Route::get('/emailverify/{token}', 'Auth\RegisterController@verifyUser')->name('emailverification');

Route::get('/notverified', function () {
  return view('notverified');
})->name('notverified')
  ->middleware('auth')
  ->middleware('can:not-view-designer');

Route::get("/editaccount", 'EditAccountController@index')->name('editaccount.index');
Route::post("/editaccount", 'EditAccountController@edit')->name('editaccount.edit');
