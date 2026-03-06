<?php

namespace App\DTO;

final class InputUserDTO
{
    private string $login;
    private string $password;
    private string $name;
    private string $role;

    private function __construct(string $login, string $password, string $name, string $role)
    {
        $this->login = $login;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }

    public static function create(string $login, string $password, string $name, string $role): InputUserDTO
    {
        return new self($login, $password, $name, $role);
    }

    public function login()
    {
        return $this->login;
    }

    public function password()
    {
        return $this->password;
    }

    public function name()
    {
        return $this->name;
    }

    public function role()
    {
        return $this->role;
    }
}
