<?php

namespace StarWars\Service\StarwarsAPI\Films;

use StarWars\DTO\API\CatalogDTO;
use StarWars\DTO\API\FilmDTO;

interface FilmsInterface
{
    public function getFilms(): CatalogDTO;
    public function getFilm($filmId): FilmDTO;
}