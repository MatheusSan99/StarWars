<?php

declare(strict_types=1);

namespace StarWars\Controller\Error;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use StarWars\Helper\HtmlRendererTrait;

class Error404Controller implements RequestHandlerInterface
{
    use HtmlRendererTrait;

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $html = $this->renderTemplate('Error/not-found', [
            'title' => 'Perdido?'
        ]);

        return new Response(200, [], $html);
    }
}