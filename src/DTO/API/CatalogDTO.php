<?php

namespace StarWars\DTO\API;

use StarWars\DTO\API\FilmDTO;

class CatalogDTO implements \JsonSerializable
{
    /**
     * @var FilmDTO[] 
     */
    private array $films = [];

    public function addFilm(FilmDTO $film): void
    {
        $this->films[] = $film;
    }

    /**
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
