<?php

declare(strict_types=1);

namespace Core;

use mysqli;

final class MySQLConnect
{
    private ?mysqli $connect = null;

    public function __construct()
    {
        $this->connect = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_NAME'],
        );

        if ($this->connect->connect_error) {
            throw new \Exception("Connection to MySQL DB failed");
        }
    }

    public function getConnection(): mysqli
    {
        return $this->connect;
    }
}
