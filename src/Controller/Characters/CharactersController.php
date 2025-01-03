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
            'title' => 'Catalogo de Personagens',
        ]);

        return new Psr7Response(200, [], $html);
    }

    public function getCharacterPage() : Psr7Response
    {
        $html = $this->renderTemplate('Characters/character', [
            'title' => 'Personagem',
        ]);

        return new Psr7Response(200, [], $html);
    }

    /**
 * @OA\Get(
 *     path="/api/external/film/{filmId}/characters",
 *     summary="Retorna os personagens de um filme específico",
 *     description="Este endpoint retorna uma lista de personagens detalhados de um filme com base no seu ID.",
 *     operationId="getCharactersByFilmId",
 *     tags={"Characters"},
 *     @OA\Parameter(
 *         name="filmId",
 *         in="path",
 *         required=true,
 *         description="ID do filme para obter os personagens",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de personagens do filme retornada com sucesso.",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/CharacterDTO")
  *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Filme não encontrado ou não possui personagens.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Filme não encontrado."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor ao tentar buscar os personagens.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Erro desconhecido ao tentar buscar os personagens."
 *             )
 *         )
 *     )
 * )
 */

    public function getCharactersByFilmId(ServerRequestInterface $request, Response $response, array $args): Response
    {
        $filmId = $args['filmId'];

        $Film = $this->getFilmCase->execute($filmId);

        $CharactersIds = $Film->getCharacters();

        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->getCharactersListCase->execute($CharactersIds)));

        return $response;
    }
}