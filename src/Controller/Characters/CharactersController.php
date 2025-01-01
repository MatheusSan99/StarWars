<?php

namespace StarWars\Controller\Characters;

use Nyholm\Psr7\Response as Psr7Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use StarWars\Helper\HtmlRendererTrait;
use StarWars\UseCases\API\GetCharacterCase;
use StarWars\UseCases\API\GetCharactersListCase;
use StarWars\UseCases\API\GetFilmCase;

class CharactersController
{
    use HtmlRendererTrait;
    private ContainerInterface $container;
    private GetFilmCase $getFilmCase;
    private GetCharactersListCase $getCharactersListCase;
    private GetCharacterCase $getCharacterCase;

    public function __construct(ContainerInterface $containerInterface, GetFilmCase $getFilmCase, GetCharactersListCase $getCharactersListCase, GetCharacterCase $getCharacterCase)
    {
        $this->container = $containerInterface;
        $this->getFilmCase = $getFilmCase;
        $this->getCharactersListCase = $getCharactersListCase;
        $this->getCharacterCase = $getCharacterCase;
    }

    public function getCharactersPage(): Psr7Response
    {
        $html = $this->renderTemplate('Characters/characters-list', [
            'titulo' => 'Catalogo de Personagens',
        ]);

        return new Psr7Response(200, [], $html);
    }

    public function getCharacterPage() : Psr7Response
    {
        $html = $this->renderTemplate('Characters/character', [
            'titulo' => 'Personagem',
        ]);

        return new Psr7Response(200, [], $html);
    }

    public function getCharactersByFilmId(ServerRequestInterface $request, Response $response, array $args): Response
    {
        $filmId = $args['filmId'];

        $Film = $this->getFilmCase->execute($filmId);

        $CharactersIds = $Film->getCharacters();

        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->getCharactersListCase->execute($CharactersIds)));

        return $response;
    }

    public function getCharacterById(ServerRequestInterface $request, Response $response, array $args): Response
    {
        $characterId = $args['characterId'];

        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->getCharacterCase->execute($characterId)));

        return $response;
    }
}