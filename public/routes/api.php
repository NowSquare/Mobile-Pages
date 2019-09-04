<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Secured app routes
Route::group(['middleware' => 'auth:api'], function() {
  Route::get('sites', '\Platform\Controllers\Site\SiteController@getSites');
  Route::get('site', '\Platform\Controllers\Site\SiteController@getSite');
});

// App authorization routes
Route::group(['prefix' => 'auth'], function () {
  Route::post('register', '\Platform\Controllers\App\AuthController@register');
  Route::post('login', '\Platform\Controllers\App\AuthController@login');
  Route::get('refresh', '\Platform\Controllers\App\AuthController@refresh');
  Route::post('password/reset', '\Platform\Controllers\App\AuthController@passwordReset');
  Route::post('password/reset/validate-token', '\Platform\Controllers\App\AuthController@passwordResetValidateToken');
  Route::post('password/update', '\Platform\Controllers\App\AuthController@passwordUpdate');

  Route::group(['middleware' => 'auth:api'], function() {
    Route::get('user', '\Platform\Controllers\App\AuthController@user'); // Get user details
    Route::post('logout', '\Platform\Controllers\App\AuthController@logout');
    Route::post('profile', '\Platform\Controllers\App\AuthController@postUpdateProfile');
  });
});