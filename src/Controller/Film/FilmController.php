<?php

namespace StarWars\Controller\Film;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use StarWars\UseCases\API\GetFilmCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response as Psr7Response;
use StarWars\Helper\HtmlRendererTrait;

class FilmController
{
    use HtmlRendererTrait;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function getFilm(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        $filmId = $args['id'];
        $getFilmCase = $this->container->get(GetFilmCase::class);
        $film = $getFilmCase->execute($filmId);

        $html = $this->renderTemplate('Film/film', [
            'titulo' => $film->getTitle(),
            'episode' => $this->getEpisodeName($film->getEpisodeId()),
            'film' => $film 
        ]);

        return new Response(200, [], $html);
    }

    private function getEpisodeName(int $episodeId): string
    {
        $episodes = [
            1 => 'Episode I - The Phantom Menace',
            2 => 'Episode II - Attack of the Clones',
            3 => 'Episode III - Revenge of the Sith',
            4 => 'Episode IV - A New Hope',
            5 => 'Episode V - The Empire Strikes Back',
            6 => 'Episode VI - Return of the Jedi',
            7 => 'Episode VII - The Force Awakens',
            8 => 'Episode VIII - The Last Jedi',
            9 => 'Episode IX - The Rise of Skywalker'
        ];

        return $episodes[$episodeId];
    }
}