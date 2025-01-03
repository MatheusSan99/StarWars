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
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $html = $this->renderTemplate('Erro/not-found', [
            'title' => 'Perdido?'
        ]);

        $this->logger->info('Usuário tentou acessar uma página inexistente: ' . $request->getUri()->getPath());

        return new Response(200, [], $html);
    }
}