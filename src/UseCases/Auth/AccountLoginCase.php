<?php

namespace StarWars\UseCases\Auth;

use StarWars\Repository\Account\AccountRepository;
use StarWars\Service\Auth\AuthService;

class AccountLoginCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }
    
    public function execute(string $email, string $password): string
    {
        $AccountModel = $this->AccountRepository->getAccountByEmail($email);
        $storedHash = $AccountModel->getPassword();

        if ($AccountModel && password_verify($password, $storedHash)) {
            $AuthService = new AuthService();
            return $AuthService->generateToken($AccountModel);
        }
         throw new \InvalidArgumentException('Invalid email or password', 401);
    }
}