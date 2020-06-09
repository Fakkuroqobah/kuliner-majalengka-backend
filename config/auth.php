<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            // 'driver' => 'passport',
            'driver' => 'jwt',
            'provider' => 'users',
        ],

        'admin' => [
            // 'driver' => 'passport',
            'driver' => 'jwt',
            'provider' => 'admin',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\User::class
        ],

        'admin' => [
            'driver' => 'eloquent',
            'model' => App\Admin::class,
        ],
    ]
];