<?php

namespace App\DTO;

final class PersistUserDTO
{
    private string $login;
    private string $name;
    private string $role;

    private function __construct(string $login, string $name, string $role)
    {
        $this->login = $login;
        $this->name = $name;
        $this->role = $role;
    }

    public static function create(string $login, string $name, string $role): PersistUserDTO
    {
        return new self($login, $name, $role);
    }

    public function login()
    {
        return $this->login;
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
