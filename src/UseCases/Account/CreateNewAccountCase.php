<?php

namespace StarWars\UseCases\Account;

use StarWars\Exceptions\Auth\RegisterException;
use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;

class CreateNewAccountCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(string $name, string $email, string $password, string $role = 'user'): AccountModel
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RegisterException(RegisterException::INVALID_EMAIL);
        }

        if (strlen($name) < 3) {
            throw new RegisterException(RegisterException::NAME_TOO_SHORT);
        }

        $user = new AccountModel(0, $name, $email, $password, $role);

        return $this->AccountRepository->createAccount($user);
    }
}