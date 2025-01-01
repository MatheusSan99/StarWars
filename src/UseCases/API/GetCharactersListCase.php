<?php

namespace StarWars\UseCases\API;

use StarWars\DTO\API\CharactersListDTO;
use StarWars\Service\StarwarsAPI\Characters\CharactersAPI;

class GetCharactersListCase
{
    private CharactersAPI $CharactersAPI;

    public function __construct(CharactersAPI $CharactersAPI)
    {
        $this->CharactersAPI = $CharactersAPI;
    }

    public function execute($charactersIdList): CharactersListDTO
    {
        return $this->CharactersAPI->getCharacters($charactersIdList);
    }
}
