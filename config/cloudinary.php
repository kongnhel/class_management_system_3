<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | កូដត្រង់នេះហើយដែលដោះស្រាយបញ្ហា "Undefined array key 'cloud'"
    |
    */
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'cloud_url' => env('CLOUDINARY_URL'),

    'url' => [
        'secure' => true,
    ],
];
