<?php

namespace StarWars\UseCases\API;

use StarWars\DTO\API\CatalogDTO;
use StarWars\Service\StarwarsAPI\Films\FilmsAPI;

class GetCatalogCase
{
    private FilmsAPI $FilmsAPI;

    public function __construct(FilmsAPI $FilmsAPI)
    {
        $this->FilmsAPI = $FilmsAPI;
    }

    public function execute(): CatalogDTO
    {
        return $this->FilmsAPI->getFilms();
    }
}
