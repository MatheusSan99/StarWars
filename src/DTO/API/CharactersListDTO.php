<?php

namespace StarWars\DTO\API;

use StarWars\DTO\API\CharacterDTO;

class CharactersListDTO implements \JsonSerializable
{
    /**
     * @var Characters[] 
     */
    private array $characters = [];

    public function addCharacter(CharacterDTO $character): void
    {
        $this->characters[] = $character;
    }

    /**
     * @return CharacterDTO[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function jsonSerialize(): array
    {
        return [
            'characters' => $this->characters
        ];
    }
}
