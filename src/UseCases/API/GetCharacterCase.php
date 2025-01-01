<?php

namespace StarWars\UseCases\API;

use StarWars\DTO\API\CharacterDTO;
use StarWars\Service\StarwarsAPI\Characters\CharactersAPI;

class GetCharacterCase
{
    private CharactersAPI $CharactersAPI;

    public function __construct(CharactersAPI $CharactersAPI)
    {
        $this->CharactersAPI = $CharactersAPI;
    }

    public function execute($characterId): CharacterDTO
    {
        return $this->CharactersAPI->getCharacter($characterId);
    }
}
