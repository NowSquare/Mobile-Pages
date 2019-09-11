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
    $config = (! is_object($account)) ? (object) $account->only('version', 'demo', 'config', 'app_name', 'app_scheme', 'app_host', 'language', 'locale', 'timezone', 'currency_code') : $account;

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