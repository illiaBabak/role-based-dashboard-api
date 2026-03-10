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

    public function isUserRole(string $token)
    {
        if (str_contains($token, "User-x-")) {
            return true;
        }
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

        if ($this->isUserRole($token)) {
            $user_id = $session['user_id'];

            $user = $this->usersModel->getUserById($user_id);

            return Response::json([
                'users' => [$user],
            ]);
        }

        return Response::json([
            'users' => $this->usersModel->getUsers(),
        ]);
    }

    public function updateUser(int $id, string $name, string $role)
    {
        $token = $_COOKIE["token"] ?? null;

        if (!$token) {
            return Response::error("Unauthorized", 401);
        }

        $session = $this->sessionsModel->getSessionByToken($token);

        if (!$session) {
            return Response::error("Unauthorized", 401);
        }

        // Check if user with role "user" is trying to update another user
        if ($session['user_id'] !== $id && $this->isUserRole($token)) {
            return Response::error("You are not allowed to update this user", 403);
        }

        $result = $this->usersModel->updateUser($id, $name, $role);

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

        // Check if user with role "user" is trying to delete another user
        if ($session['user_id'] !== $id && $this->isUserRole($token)) {
            return Response::error("You are not allowed to delete this user", 403);
        }

        $result = $this->usersModel->deleteUser($id);

        if (!$result) {
            return Response::error("Failed to delete user", 500);
        }

        return Response::json([
            'message' => 'User deleted successfully',
        ]);
    }
}
