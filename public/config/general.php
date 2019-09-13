<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Do not change settings below
     |--------------------------------------------------------------------------
     */

    // Email defaults
    'app_url' => (env('APP_ENV') == 'local') ? env('APP_URL_LOCAL', env('APP_URL')) : env('APP_URL'),
    'app_timezone' => env('APP_TIMEZONE', 'America/New_York'),
    'mail_address_from' => env('MAIL_FROM_ADDRESS'),
    'mail_name_from' => env('MAIL_FROM_NAME'),
    'mail_contact' => env('MAIL_CONTACT')
];