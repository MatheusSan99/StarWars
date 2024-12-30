<?php

namespace StarWars\Controller\UseCases\Account;

use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;

class GetAccountByEmailCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(string $name, string $email, string $password, string $role): AccountModel
    {
        $user = new AccountModel(0, $name, $email, $password, $role);
        $this->AccountRepository->createAccount($user);

        return $user;
    }
}