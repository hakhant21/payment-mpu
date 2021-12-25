<?php

return [
    'mpu' => [
        'merchantID'=> env('MERCHANT_ID'),
        'merchantSecret' => env('MERCHANT_SECRET'),
        'currencyCode' => env('CURRENCY_CODE'),
        'sandboxMode' => env('SANDBOX_MODE'),
        'baseUrl' => env('BASE_URL'),
        'prodUrl' => env('PROD_URL'),
    ]
];