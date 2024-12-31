<?php

namespace StarWars\Controller\Catalog;

use Nyholm\Psr7\Response as Psr7Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use StarWars\UseCases\API\GetCatalogCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use StarWars\Helper\HtmlRendererTrait;

class CatalogController
{
    use HtmlRendererTrait;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function catalogPage(): Psr7Response
    {
        $html = $this->renderTemplate('Catalog/movies-list', [
            'titulo' => 'Catalogo de Filmes',
        ]);
        
        return new Psr7Response(200, [], $html);
    } 

    public function getCatalog(ServerRequestInterface $request, Response $response, array $args): Response
    {
        $GetCatalogCase = $this->container->get(GetCatalogCase::class);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($GetCatalogCase->execute()));
        return $response;
    }
}