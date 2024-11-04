<?php

namespace Hilsager\GuestService\DB;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class Connect
{
    private static Connection $connect;

    public static function getConnectionInstance(array $config): Connection
    {
        if(empty(self::$connect)) {
            self::$connect = DriverManager::getConnection($config);
        }

        return self::$connect;
    }
}