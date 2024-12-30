<?php

namespace StarWars\Controller\Auth;

declare(strict_types=1);

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogoutController
{
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        setcookie('auth_token', '', time() - 43200, '/', '', true, true); 
        session_destroy();
        
        return new Response(302, ['Location' => '/login']);
    }
}