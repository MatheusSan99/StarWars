<?php

namespace StarWars\Repository\Account;

use StarWars\Exceptions\Auth\AccountException;
use StarWars\Model\Account\AccountModel;

class AccountRepository {
    
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function createAccount(AccountModel $Account) 
    {
        $statement = $this->pdo->prepare('INSERT INTO accounts (name, email, password, role) VALUES (:name, :email, :password, :role)');
        $statement->execute([
            'name' => $Account->getName(),
            'email' => $Account->getEmail(),
            'password' => $this->hashPassword($Account->getPassword()),
            'role' => $Account->getRole()
        ]);

        $Account->setId((int) $this->pdo->lastInsertId());

        return $Account;
    }

    public function getAccountByEmail(string $email): ?AccountModel
    {
        $statement = $this->pdo->prepare('SELECT * FROM accounts WHERE email = :email');
        $statement->execute([
            'email' => $email
        ]);

        $user = $statement->fetch();

        if (!$user) {
            throw new AccountException(AccountException::USER_NOT_FOUND);
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
        $statement = $this->pdo->prepare('SELECT * FROM accounts WHERE id = :id');
        $statement->execute([
            'id' => $id
        ]);

        $user = $statement->fetch();

        if (!$user) {
            throw new AccountException(AccountException::USER_NOT_FOUND);
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
        $AccountDatabase = $this->getAccountById($Account->getId());
    
        if (!$AccountDatabase) {
            throw new AccountException(AccountException::USER_NOT_FOUND);
        }
    
        if ($Account->getName() !== $AccountDatabase->getName()) {
            $statement = $this->pdo->prepare('UPDATE accounts SET name = :name WHERE id = :id');
            $statement->execute([
                'name' => $Account->getName(),
                'id' => $Account->getId()
            ]);
        }
    
        if ($Account->getEmail() !== $AccountDatabase->getEmail()) {
            $statement = $this->pdo->prepare('UPDATE accounts SET email = :email WHERE id = :id');
            $statement->execute([
                'email' => $Account->getEmail(),
                'id' => $Account->getId()
            ]);
        }
    
        if ($Account->getRole() !== $AccountDatabase->getRole()) {
            $statement = $this->pdo->prepare('UPDATE accounts SET role = :role WHERE id = :id');
            $statement->execute([
                'role' => $Account->getRole(),
                'id' => $Account->getId()
            ]);
        }
    
        if ($Account->getPassword() !== $AccountDatabase->getPassword()) {
            $hashedPassword = password_hash($Account->getPassword(), PASSWORD_ARGON2ID);
    
            $statement = $this->pdo->prepare('UPDATE accounts SET password = :password WHERE id = :id');
            $statement->execute([
                'password' => $hashedPassword,
                'id' => $Account->getId()
            ]);
        } 
    
        return $Account;
    }
    

    public function deleteAccount(int $id)
    {
        $statement = $this->pdo->prepare('DELETE FROM accounts WHERE id = :id');
        $statement->execute([
            'id' => $id
        ]);

        return $this->pdo->errorCode() === '00000';
    }
}