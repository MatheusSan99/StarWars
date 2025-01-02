<?php

namespace StarWars\UseCases\Auth;

use Psr\Log\LoggerInterface;
use StarWars\Exceptions\Auth\AuthenticationException;
use StarWars\Model\Account\AccountModel;
use StarWars\Repository\Account\AccountRepository;
use StarWars\Service\Auth\AuthService;

class AccountLoginCase
{
    private AccountRepository $AccountRepository;
    private LoggerInterface $logger;
    private AuthService $AuthService;

    public function __construct(AccountRepository $AccountRepository, LoggerInterface $logger, AuthService $AuthService)
    {
        $this->AccountRepository = $AccountRepository;
        $this->logger = $logger;
        $this->AuthService = $AuthService;
    }
    
    public function execute(string $email, string $password): AccountModel
    {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        if ($email === false || $password === false) {
            $this->logger->error('Usuário não conseguiu logar, email ou senha invalidos!', ['email' => $email, 'class' => __CLASS__, 'method' => __METHOD__]);
            throw new AuthenticationException(AuthenticationException::INVALID_CREDENTIALS);
        }
    
        $AccountModel = $this->AccountRepository->getAccountByEmail($email);
        $storedHash = $AccountModel->getPassword();

        if ($AccountModel && password_verify($password, $storedHash)) {
            $this->logger->info('Usuário logado', ['email' => $email, 'class' => __CLASS__, 'method' => __METHOD__]);
            return $AccountModel;
        }

        $this->logger->error('Usuário não conseguiu logar', ['email' => $email, 'class' => __CLASS__, 'method' => __METHOD__]);

        throw new AuthenticationException(AuthenticationException::INVALID_CREDENTIALS);
    }
}