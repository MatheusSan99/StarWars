<?php

namespace StarWars\UseCases\API;

use StarWars\DTO\API\FilmDTO;
use StarWars\Service\StarwarsAPI\Films\FilmsAPI;

class GetFilmCase 
{
    private FilmsAPI $FilmsAPI;

    public function __construct(FilmsAPI $FilmsAPI)
    {
        $this->FilmsAPI = $FilmsAPI;
    }

    public function execute($filmId): FilmDTO
    {
        return $this->FilmsAPI->getFilm($filmId);
    }
}