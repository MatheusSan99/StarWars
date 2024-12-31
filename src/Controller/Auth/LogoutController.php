<?php

declare(strict_types=1);

namespace StarWars\Controller\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use StarWars\Helper\FlashMessageTrait;

class LogoutController
{
    use FlashMessageTrait;
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logout(ServerRequestInterface $request, Response $response, array $args): ResponseInterface
    {
        setcookie('auth_token', '', time() - 43200, '/', '', true, true); 
        session_destroy();

        $this->logger->info('Usuário deslogado com sucesso');

        $this->addSuccessMessage('Usuário deslogado com sucesso');

        $response->getBody()->write(json_encode(['message' => 'Usuário deslogado com sucesso']));

        return $response;
    }
}