<?php

namespace StarWars\Repository\Account;

use StarWars\Model\Account\AccountModel;

class AccountRepository {
    
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createAccount(AccountModel $Account) 
    {
        $statement = $this->pdo->prepare('INSERT INTO account (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $statement->execute([
            'name' => $Account->getName(),
            'email' => $Account->getEmail(),
            'password' => $Account->getPassword(),
            'role' => $Account->getRole()
        ]);

        $Account->setId((int) $this->pdo->lastInsertId());

        return $this->pdo->lastInsertId();
    }

    public function getAccountByEmail(string $email): ?AccountModel
    {
        $statement = $this->pdo->prepare('SELECT * FROM account WHERE email = :email');
        $statement->execute([
            'email' => $email
        ]);

        $user = $statement->fetch();

        if (!$user) {
            throw new \Exception('Usuário não encontrado, verifi', 404);
        }

        return new AccountModel(
            (int) $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role']
        );
    }

    public function getAccountById(int $id): ?AccountModel
    {
        $statement = $this->pdo->prepare('SELECT * FROM account WHERE id = :id');
        $statement->execute([
            'id' => $id
        ]);

        $user = $statement->fetch();

        if (!$user) {
            return null;
        }

        return new AccountModel(
            (int) $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role']
        );
    }

    public function updateAccount(AccountModel $Account)
    {
        $statement = $this->pdo->prepare('UPDATE account SET name = :name, email = :email, password = :password, role = :role WHERE id = :id');
        $statement->execute([
            'id' => $Account->getId(),
            'name' => $Account->getName(),
            'email' => $Account->getEmail(),
            'password' => $Account->getPassword(),
            'role' => $Account->getRole()
        ]);

        return new AccountModel(
            (int) $Account->getId(),
            $Account->getName(),
            $Account->getEmail(),
            $Account->getPassword(),
            $Account->getRole()
        );
    }

    public function deleteAccount(int $id)
    {
        $statement = $this->pdo->prepare('DELETE FROM account WHERE id = :id');
        $statement->execute([
            'id' => $id
        ]);

        return $this->pdo->errorCode() === '00000';
    }
}