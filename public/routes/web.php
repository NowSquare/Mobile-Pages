<?php
/*
 |--------------------------------------------------------------------------
 | Installation
 |--------------------------------------------------------------------------
 */

Route::get('install', '\Platform\Controllers\InstallationController@getInstall')->name('installation');
Route::post('install', '\Platform\Controllers\InstallationController@postInstall');

/*
|--------------------------------------------------------------------------
| App routes
|--------------------------------------------------------------------------
*/

Route::get('/', '\Platform\Controllers\App\AppController@index');
