<?php

use Hilsager\GuestService\Api\RestApi;

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'guest-service',
        'user' => 'root',
        'password' => '123',
        'driver' => 'mysqli'
    ],
    'rules' => [
        RestApi::class => 'api'
    ]
];