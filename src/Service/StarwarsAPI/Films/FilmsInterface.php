<?php

namespace StarWars\Service\StarwarsAPI\Films;

use StarWars\DTO\API\CatalogDTO;

interface FilmsInterface
{
    public function getFilms(): CatalogDTO;
}