<?php

namespace StarWars\Service\StarwarsAPI\Films;

use StarWars\DTO\API\CatalogDTO;
use StarWars\DTO\API\FilmDTO;
use StarWars\Service\Connection\ConnectionInterface;

class FilmsAPI implements FilmsInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getFilms(): CatalogDTO
    {
        $url = 'https://swapi.tech/api/films/';
        $response = $this->connection->getResponse($url);
        $catalog = new CatalogDTO();

        if (isset($response['error'])) {
            return $catalog;
        }

        $movies = $response['result'];

        foreach ($movies as $movie) {
            if (!isset($movie['properties'])) {
                throw new \RuntimeException('Dados incompletos no filme.', 400);
            }
        
            $catalog->addFilm(new FilmDTO(
                $movie['uid'] ?? '1',
                $movie['properties']['title'] ?? 'Título não disponível',
                $movie['properties']['episode_id'] ?? 0,
                $movie['properties']['opening_crawl'] ?? '',
                $movie['properties']['release_date'] ?? '',
                $movie['properties']['director'] ?? 'Diretor Desconhecido',
                $movie['properties']['producer'] ?? 'Produtores Desconhecidos',
                $movie['properties']['characters'] ?? []
            ));
        }
        return $catalog; 
    }

    public function getFilm($filmId): FilmDTO
    {
        $url = 'https://swapi.tech/api/films/' . $filmId;
        $response = $this->connection->getResponse($url);

        if (isset($response['error'])) {
            throw new \RuntimeException('Filme não encontrado.', 404);
        }

        $movie = $response['result'];

        if (!isset($movie['properties'])) {
            throw new \RuntimeException('Dados incompletos no filme.', 400);
        }

        return new FilmDTO(
            $movie['properties']['uid'] ?? '1',
            $movie['properties']['title'] ?? 'Título não disponível',
            $movie['properties']['episode_id'] ?? 0,
            $movie['properties']['opening_crawl'] ?? '',
            $movie['properties']['release_date'] ?? '',
            $movie['properties']['director'] ?? 'Diretor Desconhecido',
            $movie['properties']['producer'] ?? 'Produtores Desconhecidos',
            $movie['properties']['characters'] ?? []
        );
    }
}