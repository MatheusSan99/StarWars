<?php

namespace StarWars\Controller\Catalog;

use Nyholm\Psr7\Response as Psr7Response;
use Psr\Container\ContainerInterface;
use StarWars\UseCases\API\GetCatalogCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use StarWars\Helper\HtmlRendererTrait;

class CatalogController
{
    use HtmlRendererTrait;
    private ContainerInterface $container;
    private LoggerInterface $logger;

    public function __construct(ContainerInterface $containerInterface, LoggerInterface $logger)
    {
        $this->container = $containerInterface;
        $this->logger = $logger;
    }

    public function catalogPage(): Psr7Response
    {
        $html = $this->renderTemplate('Catalog/movies-list', [
            'title' => 'Catalogo de Filmes',
        ]);
        
        return new Psr7Response(200, [], $html);
    } 

    /**
 * @OA\Get(
 *     path="/api/external/catalog",
 *     summary="Retorna o catálogo de filmes",
 *     description="Este endpoint retorna um catálogo com uma lista de filmes disponíveis.",
 *     operationId="getCatalog",
 *     tags={"Catalog"},
 *     @OA\Response(
 *         response=200,
 *         description="Catálogo de filmes retornado com sucesso.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="films",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/FilmDTO")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor ao tentar buscar o catálogo.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Erro desconhecido ao tentar buscar o catálogo."
 *             )
 *         )
 *     )
 * )
 */

    public function getCatalog(ServerRequestInterface $request, Response $response, array $args): Response
    {
        try {
            $GetCatalogCase = $this->container->get(GetCatalogCase::class);
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($GetCatalogCase->execute()));
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Erro ao tentar buscar o catálogo: ', ['error' => $e->getMessage()]);
            $response->getBody()->write(json_encode(['message' => 'Erro ao tentar buscar o catálogo: ' . $e->getMessage()]));
            return $response->withStatus(500);
        }
    }
}