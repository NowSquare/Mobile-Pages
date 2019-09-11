<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\User;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Fix for "Specified key was too long error" error
        // https://laravel-news.com/laravel-5-4-key-too-long-error
        \Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Override filesystem config for media url
        config(['filesystems.disks.media.url' => request()->getSchemeAndHttpHost() . '/media']);

        $account = null;

        $account_config = [
          'pusher' => [
            'key' => config('broadcasting.connections.pusher.key'),
            'app_id' => config('broadcasting.connections.pusher.app_id'),
            'options' => config('broadcasting.connections.pusher.options')
          ]
        ];

        if (! $this->app->runningInConsole() && \Platform\Controllers\InstallationController::isInstalled() && \Request::segment(1) != 'install' && \Schema::hasTable('users')) {
          $hostname = $this->app['request']->getHost();

          if (auth('api')->check()) {
            $account = auth('api')->user()->account;
          } else {
            $account = User::withoutGlobalScopes()->where('app_host', $hostname)->first();
          }

          // If url is not resolved, check app
          if ($account === null) {
            $app = \Platform\Models\Site::withoutGlobalScopes()->where('active', 1)->where('host', $hostname)->first();
            if ($app !== null) {
              $account = $app->account;
              $account->app_id = $app->id;
            }
          }

          //$account = (app()->bound('account')) ? app()->make('account') : null;
          if ($account !== null) {
            $account->found = true;
            if (! isset($account->app_id)) $account->app_id = null;

            $this->app->instance('account', $account);

            // Override config
            if ($account->app_name !== null) config(['app.name' => $account->app_name]);
            config(['app.url' => request()->getSchemeAndHttpHost()]);
            //if ($account->app_host !== null) config(['app.url' => $account->app_host]);
            if ($account->app_mail_name_from !== null) config(['general.mail_name_from' => $account->app_mail_name_from]);
            if ($account->app_mail_address_from !== null) config(['general.mail_address_from' => $account->app_mail_address_from]);

            if ($account->app_name === null) $account->app_name = config('app.name');
            if ($account->app_host === null) $account->app_host = request()->getHost();
            $account->app_scheme = request()->getScheme();
            $account->version = config('versions.script_version');
            if (env('APP_DEMO', false) === true) $account->demo = true;

            config(['general.cname_domain' => $account->app_host]);

            $account->config = [
              'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'app_id' => config('broadcasting.connections.pusher.app_id'),
                'options' => config('broadcasting.connections.pusher.options')
              ]
            ];

            // Variables for views
            view()->composer('*', function ($view) use ($account) {
              // view()->share('key', 'value');
            });
          }
        }

        if ($account === null) {
            $account = new \stdClass;
            $account->found = false;
            $account->app_name = config('app.name');
            $account->language = config('system.default_language');
            $account->locale = config('system.default_locale');
            $account->timezone = config('system.default_timezone');
            $account->currency_code = config('system.default_currency');
            $account->app_host = request()->getHost();
            $account->app_scheme = request()->getScheme();
            $account->version = config('versions.script_version');
            $account->config = $account_config;
            if (env('APP_DEMO', false) === true) $account->demo = true;

            $account = $account;

            $this->app->instance('account', $account);
        }
    }
}
