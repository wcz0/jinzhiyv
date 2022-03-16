<?php

declare(strict_types=1);

return [
    'username' => env('MAIL_USERNAME', ''),
    'password' => env('MAIL_PASSWORD', ''),
    'from' => env('MAIL_FROM_NAME', 'notice'),
    'host' => env('MAIL_HOST', 'smtp.qiye.aliyun.com'),
    'auth' => env('MAIL_AUTH', true),
    'secure' => env('MAIL_SECURE', 'ssl'),
    'port' => env('MAIL_PORT', 465),
];