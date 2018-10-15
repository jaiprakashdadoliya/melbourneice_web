<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [ 
            'client_id' => '1351884644935003',
            'client_secret' => '65b0b8713f4d95e1fe8567bb99f09264',
            'redirect' => 'http://www.fxbytes.com/Client/melbourneIce/callback/facebook' 
    ],

    'google' => [ 
            'client_id' => '205472483358-9t5m5177ral5ffrk2fd7lih2phdn9ush.apps.googleusercontent.com',
            'client_secret' => 'ADxH3DIsl09FsUUdFXt7MHkI',
            'redirect' => 'http://www.fxbytes.com/Client/melbourneIce/callback/google' 
    ],

];
