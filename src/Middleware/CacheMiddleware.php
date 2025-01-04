<?php

namespace StarWars\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CacheMiddleware implements MiddlewareInterface
{
    private $noCacheRoutes = ['/pages/login', '/pages/create-account'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $route = $request->getUri()->getPath();

        if (in_array($route, $this->noCacheRoutes)) {
            $response = $response->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate')
                ->withHeader('Pragma', 'no-cache')
                ->withHeader('Expires', '0');
        } else {
            $response = $response->withHeader('Cache-Control', 'public, max-age=3600');
        }

        return $response;
    }
}
