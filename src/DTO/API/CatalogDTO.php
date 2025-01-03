<?php

namespace StarWars\DTO\API;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CatalogDTO",
 *     type="object",
 *     required={"films"},
 *     @OA\Property(
 *         property="films",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FilmDTO"),
 *         description="Lista de filmes no catálogo"
 *     )
 * )
 */
class CatalogDTO implements \JsonSerializable
{
    /**
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
