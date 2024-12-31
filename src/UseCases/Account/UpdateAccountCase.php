<?php

namespace StarWars\UseCases\Account;

use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;

class UpdateAccountCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(int $id, string $name, string $email, string $password, string $role): AccountModel
    {
        $user = new AccountModel($id, $name, $email, $password, $role);
        
        return $this->AccountRepository->updateAccount($user);
    }
}