<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',

    'user_avatar' => [
        'width' => 300,
        'height' => 300,
        'quality' => 60,
    ],

    'product' => [
        'thumb' => [
            'width' => 300,
            'height' => 350,
            'quality' => 60,
        ],
        'max_images' => 6,
    ]

];
