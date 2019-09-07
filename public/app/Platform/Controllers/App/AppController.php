<?php 

namespace Platform\Controllers\App;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AppController extends \App\Http\Controllers\Controller {

  /*
   |--------------------------------------------------------------------------
   | Single Page Application Controller for Web App
   |--------------------------------------------------------------------------
   |
   | App logic
   |--------------------------------------------------------------------------
   */

  /**
   * Initialize the app
   *
   * @return \Illuminate\View\View
   */
  public function index() {
    $account = app()->make('account');
    if (env('APP_DEMO', false) === true) $account->demo = true;
    $config = (object) $account->only('version', 'config', 'app_name', 'app_headline', 'app_scheme', 'app_host', 'language', 'locale');

    $config = json_encode($config);

    return view('app', compact('account', 'config'));
  }
  /**
   * This page is shown when no account is matched with the host
   *
   * @return \Illuminate\View\View
   */
  public function getAccountNotFound() {
    $account = app()->make('account');
    return view('account-404', compact('account'));
  }

}