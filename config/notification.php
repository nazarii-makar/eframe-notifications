<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    */
    'channels' => [

        'smsfly' => [
            'login'    => env('SMSFLY_LOGIN'),
            'password' => env('SMSFLY_PASSWORD'),

            'alfaname' => env('SMSFLY_ALFANAME'),
            'cost'     => env('SMSFLY_COST'),
        ]

    ]
];