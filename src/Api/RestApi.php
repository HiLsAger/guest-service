<?php

namespace Hilsager\GuestService\Api;

use Hilsager\GuestService\Api\Guest\GuestApi;
use Hilsager\GuestService\Rest;

class RestApi extends Rest
{
    protected string $restName = 'api';

    protected array $entities = [
        'guest' => GuestApi::class
    ];
}