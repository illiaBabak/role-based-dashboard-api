<?php

declare(strict_types=1);

namespace App\Models;

use App\DTO\PersistUserDTO;
use mysqli;
use Core\MySQLConnect;

final class UsersModel
{
    private ?mysqli $connect = null;

    public function __construct()
    {
        $this->connect = new MySQLConnect()->getConnection();
    }

    public function getUserByLogin(string $login)
    {
        $stmt = $this->connect->prepare("SELECT id,login, hash_password, name, role FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        return $user ?: null;
    }

    public function createUser(PersistUserDTO $user, string $hash_password)
    {
        $login = $user->login();
        $name = $user->name();
        $role = $user->role();

        $stmt = $this->connect->prepare("INSERT INTO users (id, login, hash_password, name, role) VALUES (1, ?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $hash_password, $name, $role);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }
}
