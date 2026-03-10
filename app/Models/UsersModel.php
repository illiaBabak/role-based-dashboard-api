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
        $stmt = $this->connect->prepare("SELECT id, hash_password, login, name, role FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        return $user ?? null;
    }

    public function getUserById(int $id)
    {
        $stmt = $this->connect->prepare("SELECT id, login, name, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        return $user ?? null;
    }

    public function getUsers()
    {
        $stmt = $this->connect->prepare("SELECT id, login, name, role FROM users");
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createUser(PersistUserDTO $user, string $hash_password): int
    {
        $login = $user->login();
        $name = $user->name();
        $role = $user->role();

        $stmt = $this->connect->prepare("INSERT INTO users (login, hash_password, name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $login, $hash_password, $name, $role);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function updateUser(int $id, string $name, string $role)
    {
        $stmt = $this->connect->prepare("UPDATE users SET name = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $role, $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function deleteUser(int $id)
    {
        $stmt = $this->connect->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }
}
