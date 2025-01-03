<?php

namespace StarWars\DTO\API;

use OpenApi\Annotations as OA;

class CharactersListDTO implements \JsonSerializable
{
    /**
     * @OA\Property(
     *     property="characters",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/CharacterDTO"),
     *     description="Lista de personagens detalhados",
     *     example={
     *         {
     *             "id": 1,
     *             "name": "Luke Skywalker",
     *             "height": "172",
     *             "mass": "77",
     *             "hair_color": "blond",
     *             "skin_color": "fair",
     *             "eye_color": "blue",
     *             "birth_year": "19BBY",
     *             "gender": "male"
     *         },
     *         {
     *             "id": 2,
     *             "name": "Darth Vader",
     *             "height": "202",
     *             "mass": "136",
     *             "hair_color": "none",
     *             "skin_color": "white",
     *             "eye_color": "yellow",
     *             "birth_year": "41.9BBY",
     *             "gender": "male"
     *         }
     *     }
     * )
     * @var CharacterDTO[]
     */
    private array $characters = [];

    /**
     * Adiciona um personagem à lista.
     *
     * @param CharacterDTO $character
     * @return void
     */
    public function addCharacter(CharacterDTO $character): void
    {
        $this->characters[] = $character;
    }

    /**
     * Retorna a lista de personagens.
     *
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
