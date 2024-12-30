<?php

namespace StarWars\Controller\UseCases\Auth;

use StarWars\Model\AccountModel;
use StarWars\Repository\AccountRepository;

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