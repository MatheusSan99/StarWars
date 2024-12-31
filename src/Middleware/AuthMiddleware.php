<?php

namespace StarWars\Middleware;

use Firebase\JWT\JWT;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use StarWars\Service\Auth\AuthService;
use StarWars\UseCases\Account\GetAccountByEmailCase;

class AuthMiddleware implements MiddlewareInterface
{
    private GetAccountByEmailCase $GetAccountByTokenCase;
    private ContainerInterface $container;
    private LoggerInterface $logger;
    private AuthService $AuthService;

    public function __construct(GetAccountByEmailCase $GetAccountByTokenCase, ContainerInterface $containerInterface, LoggerInterface $logger, AuthService $AuthService)
    {
        $this->GetAccountByTokenCase = $GetAccountByTokenCase;
        $this->container = $containerInterface;
        $this->logger = $logger;
        $this->AuthService = $AuthService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (array_key_exists('logged', $_SESSION) && $_SESSION['logged'] === true) {
            return $handler->handle($request);
        }

        $token = $request->getHeader('Authorization')[0] ?? null;   

        if ($token) {
            try {
                $validToken = $this->AuthService->validateToken($token);
                $this->GetAccountByTokenCase->execute($validToken->email);

                $_SESSION['logged'] = true;

                return $handler->handle($request);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        return new Response(302, new \Slim\Psr7\Headers(['Location' => '/pages/login']));
    }
}
