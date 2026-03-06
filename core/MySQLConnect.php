<?php

declare(strict_types=1);

namespace Core;

use mysqli;

final class MySQLConnect
{
    private ?mysqli $connect = null;

    public function __construct()
    {
        $db = "rbd";
        $host = "127.0.0.1";
        $user = "root";
        $password = "";

        $this->connect = new mysqli($host, $user, $password, $db);

        if ($this->connect->connect_error) {
            throw new \Exception("Connection to MySQL DB failed");
        }
    }

    public function getConnection(): mysqli
    {
        return $this->connect;
    }
}
