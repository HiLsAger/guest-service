<?php

use Hilsager\GuestService\Api\RestApi;

return [
    'db' => [
        'host' => 'db',
        'dbname' => 'myDb',
        'user' => 'user',
        'password' => 'test',
        'driver' => 'mysqli'
    ],
    'rules' => [
        RestApi::class => 'api'
    ]
];