<?php

namespace StarWars\Model\Auth;

class AccountModel
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private string $role;

    public function __construct(int $id, string $name, string $email, string $password, string $role)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function changeEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email', 400);
        }
        $this->email = $email;
    }

    public function changePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters', 400);
        }

        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role
        ];
    }

}