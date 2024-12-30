<?php

namespace StarWars\Controller\UseCases\Account;

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
        $user = new AccountModel(0, $name, $email, $password, $role);

        return $this->AccountRepository->createAccount($user);
    }
}