<?php

namespace StarWars\Controller\Doc;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use StarWars\Helper\HtmlRendererTrait;

class DocumentationController
{
    use HtmlRendererTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function docPage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $html = $this->renderTemplate('Doc/api-doc', [
            'titulo' => 'Documentação da API'
        ]);

        return new Response(200, [], $html);
    }

    public function getApiDoc(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $swaggerJson = file_get_contents(__DIR__ . '/../../../swagger.json');

        return new Response(200, ['Content-Type' => 'application/json'], $swaggerJson);
    }
    
}