<?php

namespace StarWars\Controller\UseCases\Account;

use StarWars\Repository\Account\AccountRepository;

class DeleteAccountCase
{
    private AccountRepository $AccountRepository;

    public function __construct(AccountRepository $AccountRepository)
    {
        $this->AccountRepository = $AccountRepository;
    }

    public function execute(int $id): bool
    {
        return $this->AccountRepository->deleteAccount($id);
    }
}