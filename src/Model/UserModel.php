<?php

use Psr\Log\LoggerInterface;

class UserModel
{
    private PDO $pdo;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, PDO $pdo)
    {
        $this->logger = $logger;
        $this->pdo = $pdo;
    }

    public function insertUser(string $name, string $email, string $password): void
    {
        $this->logger->info('InsertUser: ' . $email);

        $hash = password_hash($password, PASSWORD_ARGON2ID);
        $sql = 'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?);';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $name);
        $statement->bindValue(2, $email);
        $statement->bindValue(3, $hash);
        $statement->bindValue(4, 'user');
        $statement->execute();
    }

    public function updateUser(string $name, string $email, string $password): void
    {
        $this->logger->info('UpdateUser: ' . $email);

        $hash = password_hash($password, PASSWORD_ARGON2ID);
        $sql = 'UPDATE users SET name = ?, password = ? WHERE email = ?;';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $name);
        $statement->bindValue(2, $hash);
        $statement->bindValue(3, $email);
        $statement->execute();
    }

    public function deleteUser(string $email): void
    {
        $this->logger->info('DeleteUser: ' . $email);

        $sql = 'DELETE FROM users WHERE email = ?;';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();
    }

    public function checkUser(string $email, string $password): bool
    {
        $sql = 'SELECT * FROM users WHERE email = ?;';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();
        $user = $statement->fetch();

        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    public function isAdmin(string $email): bool
    {
        $sql = 'SELECT * FROM users WHERE email = ?;';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();
        $user = $statement->fetch();

        if (!$user) {
            return false;
        }

        return $user['role'] === 'admin';
    }
}