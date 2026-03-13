<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UsersModel;
use Core\Response;
use App\Models\SessionsModel;

final class UsersService
{
    private UsersModel $usersModel;
    private SessionsModel $sessionsModel;
    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->sessionsModel = new SessionsModel();
    }

    public function getUsers()
    {
        $token = $_COOKIE["token"] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $session = $this->sessionsModel->getSessionByToken($token);

        if (!$session) {
            return Response::error("Unauthorized", 401);
        }

        $user = $this->usersModel->getUserById($session['user_id']);

        if (!$user) {
            return Response::error("Unauthorized", 401);
        }

        if ($user['role'] === 'user') {
            return Response::json([
                'users' => [$user],
            ]);
        }

        return Response::json([
            'users' => $this->usersModel->getUsers(),
        ]);
    }

    public function updateUser(int $id, ?string $name, ?string $role)
    {
        $token = $_COOKIE["token"] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $session = $this->sessionsModel->getSessionByToken($token);

        if (!$session) {
            return Response::error("Unauthorized", 401);
        }

        $user = $this->usersModel->getUserById($id);

        if (!$user) {
            return Response::error("Unauthorized", 401);
        }

        // Check if user with role "user" is trying to update another user
        if ($session['user_id'] !== $id && $user['role'] === 'user') {
            return Response::error("You are not allowed to update this user", 403);
        }

        $nameToUpdate = $name ?? $user['name'];
        $roleToUpdate = $role ?? $user['role'];

        $result = $this->usersModel->updateUser($id, $nameToUpdate, $roleToUpdate);

        if (!$result) {
            return Response::error("Failed to update user", 500);
        }

        return Response::json([
            'message' => 'User updated successfully',
        ]);
    }

    public function deleteUser(int $id)
    {
        $token = $_COOKIE["token"] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $session = $this->sessionsModel->getSessionByToken($token);

        if (!$session) {
            return Response::error("Unauthorized", 401);
        }

        $user = $this->usersModel->getUserById($id);

        if (!$user) {
            return Response::error("Unauthorized", 401);
        }

        // Check if user with role "user" is trying to delete another user
        if ($session['user_id'] !== $id && $user['role'] === 'user') {
            return Response::error("You are not allowed to delete this user", 403);
        }

        $this->sessionsModel->deleteSessionByUserId($id);

        $result = $this->usersModel->deleteUser($id);

        if (!$result) {
            return Response::error("Failed to delete user", 500);
        }

        return Response::json([
            'message' => 'User deleted successfully',
        ]);
    }
}
