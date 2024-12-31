<?php

namespace StarWars\Controller\Film;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use StarWars\UseCases\API\GetFilmCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response as Psr7Response;
use StarWars\Helper\HtmlRendererTrait;

class FilmController
{
    use HtmlRendererTrait;

    private ContainerInterface $container;
    private GetFilmCase $getFilmCase;

    public function __construct(ContainerInterface $containerInterface, GetFilmCase $getFilmCase)
    {
        $this->container = $containerInterface;
        $this->getFilmCase = $getFilmCase;
    }

    public function getFilmPage(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        $html = $this->renderTemplate('Film/film', []);

        return new Response(200, [], $html);
    }

    public function getFilmById(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($this->getFilmCase->execute($id)));
        return $response;
    }
}