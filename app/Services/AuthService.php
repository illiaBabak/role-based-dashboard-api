<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\InputUserDTO;
use App\DTO\PersistUserDTO;
use App\Models\SessionsModel;
use Core\Response;
use App\Models\UsersModel;

function generateRandomString(int $length = 10)
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

final class AuthService
{
    private UsersModel $usersModel;
    private SessionsModel $sessions_model;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->sessions_model = new SessionsModel();
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

    public function me()
    {
        $token = $_COOKIE["token"] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $session = $this->sessions_model->getSessionByToken($token);

        if (!$session) {
            return Response::error("Unauthorized", 401);
        }

        if (strtotime($session['expires_at']) < time()) {
            return Response::error('Session expired', 401);
        }

        $user = $this->usersModel->getUserById($session['user_id']);

        if (!$user) {
            return Response::error('Unauthorized', 401);
        }

        $safeUser = [
            'id' => $user['id'],
            'login' => $user['login'],
            'name' => $user['name'],
            'role' => $user['role'],
        ];

        return Response::json([
            'user' => $safeUser,
        ]);
    }

    public function logout()
    {
        $token = $_COOKIE['token'] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $this->sessions_model->deleteSessionByToken($token);

        setcookie('token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return Response::json([
            'message' => 'Logged out',
        ]);
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

        $userId = $this->usersModel->createUser($user, $hash_password);

        if (!$userId) {
            return Response::error('Failed to create user');
        }

        $token = $this->generateToken($role);

        $expiresAt = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7); // 7 days

        $this->sessions_model->createSession($userId, $token, $expiresAt);

        setcookie(
            'token',
            $token,
            [
                'expires' => time() + 60 * 60 * 24 * 7, // 7 days
                'path' => '/',
                'domain' => '',
                'secure' => false, // true in HTTPS production
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        $payload = [
            "user" => [
                "id" => $userId,
                "login" => $login,
                "name" => $name,
                "role" => $role,
            ],
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

        $expiresAt = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 7); // 7 days

        $this->sessions_model->createSession($user['id'], $token, $expiresAt);

        setcookie(
            'token',
            $token,
            [
                'expires' => time() + 60 * 60 * 24 * 7, // 7 days
                'path' => '/',
                'domain' => '',
                'secure' => false, // true in HTTPS production
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        $payload = [
            "user" => [
                "id" => $user['id'],
                "login" => $user['login'],
                "name" => $user['name'],
                "role" => $user['role'],
            ],
        ];

        return Response::json($payload);
    }
}
