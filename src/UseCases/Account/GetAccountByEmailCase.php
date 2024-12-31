<?php

namespace StarWars\UseCases\Account;

use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;

class GetAccountByEmailCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(string $email): AccountModel
    {
        return $this->AccountRepository->getAccountByEmail($email);
    }
}