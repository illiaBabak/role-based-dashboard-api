<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\InputUserDTO;
use App\DTO\PersistUserDTO;
use Core\Response;
use App\Models\UsersModel;

function generateRandomString(int $length = 10)
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

final class AuthService
{
    private UsersModel $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function generateToken(string $role)
    {
        $admin_part = "Admin-x-";
        $user_part = "User-x-";

        if ($role === "admin") {
            return $admin_part . generateRandomString(10);
        } else {
            return $user_part . generateRandomString(10);
        }
    }

    public function registerUser(InputUserDTO $user)
    {
        $name = $user->name();
        $login = $user->login();
        $password = $user->password();
        $role = $user->role();

        if (!$name || !$login || !$password || !$role) {
            return Response::error('Name, login, password and role are required');
        }

        if (strlen($password) < 6) {
            return Response::error('Password must be at least 6 characters long');
        }

        if ($this->usersModel->getUserByLogin($login)) {
            return Response::error('User with this login already exists');
        }

        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        $user = PersistUserDTO::create($login, $name, $role);

        $isCreated = $this->usersModel->createUser($user, $hash_password);

        if (!$isCreated) {
            return Response::error('Failed to create user');
        }

        $token = $this->generateToken($role);

        $payload = [
            "token" => $token,
            "user" => $user,
        ];

        return Response::json($payload);
    }

    public function loginUser(string $login, string $password)
    {
        $user = $this->usersModel->getUserByLogin($login);

        if (!$user) {
            return Response::error('User with this login does not exist');
        }

        if (!password_verify($password, $user['hash_password'])) {
            return Response::error('Invalid credentials');
        }

        $token = $this->generateToken($user['role']);

        $payload = [
            "token" => $token,
            "user" => $user,
        ];

        return Response::json($payload);
    }
}
