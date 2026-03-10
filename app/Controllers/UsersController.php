<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsersService;
use Core\Response;

final class UsersController
{
    private UsersService $usersService;

    public function __construct()
    {
        $this->usersService = new UsersService();
    }

    public function getUsers()
    {
        return $this->usersService->getUsers();
    }

    public function updateUser(int $id)
    {
        if (!$id) {
            return Response::error('Id is required');
        }

        $body = Response::getBody();

        if (!$body) {
            return Response::error('Body is required');
        }

        $name = $body->name;
        $role = $body->role;

        if (!$name && !$role) {
            return Response::error('Name or role are required');
        }

        return $this->usersService->updateUser($id, $name, $role);
    }

    public function deleteUser(int $id)
    {
        if (!$id) {
            return Response::error('Id is required');
        }

        return $this->usersService->deleteUser($id);
    }
}
