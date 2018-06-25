<?php

/*
|--------------------------------------------------------------------------
| "Informational" routes
|--------------------------------------------------------------------------
|
| Routes for the static pages containing various information about the site.
|
 */

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
})->middleware('auth')
  ->name('userguide');

Route::get('/disclaimer',function() {
	return view('disclaimer');
})->name('disclaimer');
