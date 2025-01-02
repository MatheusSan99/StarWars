<?php

declare(strict_types=1);

namespace StarWars\Controller\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use StarWars\Helper\FlashMessageTrait;
use StarWars\UseCases\Auth\LogoutCase;

class LogoutController
{
    use FlashMessageTrait;

    private LogoutCase $LogoutCase;
    
    public function __construct(LogoutCase $LogoutCase)
    {
        $this->LogoutCase = $LogoutCase;
    }

    public function logout(ServerRequestInterface $request, Response $response, array $args): ResponseInterface
    {
        $message = $this->LogoutCase->execute();

        $response->getBody()->write(json_encode(['message' => $message]));

        return $response;
    }
}