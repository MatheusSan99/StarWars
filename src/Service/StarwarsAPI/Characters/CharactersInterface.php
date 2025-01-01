<?php

namespace StarWars\Service\StarwarsAPI\Characters;

use StarWars\DTO\API\CharacterDTO;
use StarWars\DTO\API\CharactersListDTO;

interface CharactersInterface
{
    public function getCharacters(array $listId): CharactersListDTO;
    public function getCharacter($id): CharacterDTO;
}