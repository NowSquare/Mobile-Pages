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

// Public routes
Route::get('site-by-slug', '\Platform\Controllers\Site\SiteController@getSiteBySlug');

Route::group(['prefix' => 'localization'], function() {
  Route::get('locales', '\Platform\Controllers\Core\Localization@getLocales');
  Route::get('timezones', '\Platform\Controllers\Core\Localization@getTimezones');
  Route::get('currencies', '\Platform\Controllers\Core\Localization@getCurrencies');
});

// Secured app routes
Route::group(['middleware' => 'auth:api'], function() {
  Route::get('sites', '\Platform\Controllers\Site\SiteController@getSites');
  Route::get('site', '\Platform\Controllers\Site\SiteController@getSite');
  Route::post('site/create-site', '\Platform\Controllers\Site\SiteController@postCreateSite');
  Route::post('site/save-site', '\Platform\Controllers\Site\SiteController@postSaveSite');
  Route::post('site/delete-site', '\Platform\Controllers\Site\SiteController@postDeleteSite');
  Route::post('site/add-page', '\Platform\Controllers\Site\SiteController@postAddPage');
  Route::post('site/save-page', '\Platform\Controllers\Site\SiteController@postSavePage');
  Route::post('site/move-page', '\Platform\Controllers\Site\SiteController@postMovePage');
  Route::post('site/delete-page', '\Platform\Controllers\Site\SiteController@postDeletePage');
});

// App authorization routes
Route::group(['prefix' => 'auth'], function () {
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