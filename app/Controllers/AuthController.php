<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Response;
use App\DTO\InputUserDTO;
use App\Services\AuthService;

final class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function getUser()
    {
        return $this->authService->me();
    }

    public function createUser()
    {
        $body = Response::getBody();

        if (!$body) {
            return Response::error('Body is required');
        }

        $name = $body->name;
        $login = $body->login;
        $password = $body->password;
        $role = $body->role;

        $user = InputUserDTO::create($login, $password, $name, $role);

        return $this->authService->registerUser($user);
    }

    public function loginUser()
    {
        $body = Response::getBody();

        if (!$body) {
            return Response::error('Body is required');
        }

        $login = $body->login;
        $password = $body->password;

        return $this->authService->loginUser($login, $password);
    }
}
