<?php

namespace StarWars\UseCases\Account;

use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;

class GetAccountByIdCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(int $id): AccountModel
    {
        $user = $this->AccountRepository->getAccountById($id);

        if (!$user) {
            throw new \InvalidArgumentException('User not found', 404);
        }

        return $user;
    }
}