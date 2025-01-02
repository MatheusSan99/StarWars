<?php

namespace StarWars\UseCases\Auth;

use Psr\Log\LoggerInterface;
use StarWars\Service\Auth\AuthService;

class LogoutCase
{
    private LoggerInterface $logger;
    private AuthService $AuthService;

    public function __construct(LoggerInterface $logger, AuthService $AuthService)
    {
        $this->logger = $logger;
        $this->AuthService = $AuthService;
    }
    
    public function execute(): string
    {
        $this->AuthService->logout();

        $message = 'Usuário deslogado com sucesso';

        $this->logger->info('Usuário deslogado com sucesso');

        return $message;
    }
}