<?php

use Hilsager\GuestService\Api\RestApi;

return [
    'db' => [
        'host' => 'db',
        'dbname' => 'guest-service',
        'user' => 'user',
        'password' => 'test',
        'driver' => 'mysqli'
    ],
    'rules' => [
        RestApi::class => 'api'
    ]
];