<?php

namespace StarWars\Controller\Film;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use StarWars\UseCases\API\GetFilmCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response as Psr7Response;
use StarWars\Helper\HtmlRendererTrait;

class FilmController
{
    use HtmlRendererTrait;

    private ContainerInterface $container;
    private GetFilmCase $getFilmCase;
    private LoggerInterface $logger;

    public function __construct(ContainerInterface $containerInterface, GetFilmCase $getFilmCase, LoggerInterface $logger)
    {
        $this->container = $containerInterface;
        $this->getFilmCase = $getFilmCase;
        $this->logger = $logger;
    }

    public function getFilmPage(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        $html = $this->renderTemplate('Film/film', []);

        return new Response(200, [], $html);
    }

    public function getFilmById(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        try {
            $id = $args['id'];
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($this->getFilmCase->execute($id)));
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao buscar filme: ' . $e->getMessage());
            return new Response(500, [], json_encode(['message' => 'Erro desconhecido ao buscar filme.']));
        }
    }
}