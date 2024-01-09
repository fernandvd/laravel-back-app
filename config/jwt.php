<?php

return [
    'expiration' => (int) env('JWT_EXPIRATION', 3600), // SECONDS
    'headers' => [
        'alg' => 'HS256',
        'typ' => 'JWT',
    ]
];