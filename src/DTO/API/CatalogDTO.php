<?php

namespace StarWars\DTO\API;

use OpenApi\Annotations as OA;

class CatalogDTO implements \JsonSerializable
{
    /**
     * @OA\Property(
     *     property="films",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/FilmDTO"),
     *     description="Lista de filmes no catálogo",
     *     example={ 
     *         { 
     *             "id": 1, 
     *             "title": "Star Wars: Episode IV - A New Hope",
     *             "episode_id": 4,
     *             "opening_crawl": "It is a period of civil war...",
     *             "release_date": "1977-05-25",
     *             "director": "George Lucas",
     *             "producers": "George Lucas, Gary Kurtz",
     *             "characters": [1, 2, 3],
     *             "isFavorite": true,
     *             "isOnDatabase": true
     *         },
     *         { 
     *             "id": 2, 
     *             "title": "Star Wars: Episode V - The Empire Strikes Back",
     *             "episode_id": 5,
     *             "opening_crawl": "It is a dark time for the rebellion...",
     *             "release_date": "1980-05-21",
     *             "director": "Irvin Kershner",
     *             "producers": "George Lucas, Gary Kurtz",
     *             "characters": [4, 5, 6],
     *             "isFavorite": false,
     *             "isOnDatabase": true
     *         }
     *     }
     * )
     * @var FilmDTO[]
     */
    private array $films = [];

    /**
     * Adiciona um filme ao catálogo.
     *
     * @param FilmDTO $film
     * @return void
     */
    public function addFilm(FilmDTO $film): void
    {
        $this->films[] = $film;
    }

    /**
     * Retorna a lista de filmes no catálogo.
     *
     * @return FilmDTO[]
     */
    public function getFilms(): array
    {
        return $this->films;
    }

    public function jsonSerialize(): array
    {
        return [
            'films' => $this->films
        ];
    }
}
