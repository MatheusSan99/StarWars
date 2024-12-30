<?php

namespace StarWars\Controller\UseCases\Auth;

use StarWars\Model\Auth\AccountModel;
use StarWars\Repository\Auth\AccountRepository;

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
        $this->AccountRepository->createAccount($user);

        return $user;
    }
}