<?php

namespace StarWars\UseCases\Auth;

use StarWars\Exceptions\Auth\AuthenticationException;
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
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
        if ($email === false || $password === false) {
            throw new AuthenticationException(AuthenticationException::INVALID_CREDENTIALS);
        }
    
        $AccountModel = $this->AccountRepository->getAccountByEmail($email);
        $storedHash = $AccountModel->getPassword();

        if ($AccountModel && password_verify($password, $storedHash)) {
            $AuthService = new AuthService();
            return $AuthService->generateToken($AccountModel);
        }

        throw new AuthenticationException(AuthenticationException::INVALID_CREDENTIALS);
    }
}