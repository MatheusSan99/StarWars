<?php

namespace StarWars\Service\StarwarsAPI\Characters;

use StarWars\DTO\API\CharacterDTO;
use StarWars\DTO\API\CharactersListDTO;
use StarWars\Service\Connection\ConnectionInterface;
use StarWars\Service\StarwarsAPI\Characters\CharactersInterface;

class CharactersAPI implements CharactersInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getCharacters(array $listId) : CharactersListDTO
    {
        $characters = new CharactersListDTO();

        foreach ($listId as $id) {
            $url = 'https://swapi.tech/api/people/' . $id;
            $response = $this->connection->getResponse($url);

            if (isset($response['error'])) {
                continue;
            }

            $character = $response['result'];

            if (!isset($character['properties'])) {
                throw new \RuntimeException('Dados incompletos no personagem.', 400);
            }

            $characters->addCharacter(new CharacterDTO(
                $character['uid'] ?? '1',
                $character['properties']['name'] ?? 'Nome não disponível',
                $character['properties']['height'] ?? 0,
                $character['properties']['mass'] ?? 0,
                $character['properties']['hair_color'] ?? 'Cor do cabelo não disponível',
                $character['properties']['skin_color'] ?? 'Cor da pele não disponível',
                $character['properties']['eye_color'] ?? 'Cor dos olhos não disponível',
                $character['properties']['birth_year'] ?? 'Ano de nascimento não disponível',
                $character['properties']['gender'] ?? 'Gênero não disponível'
            ));

        }

        return $characters;
    }

    public function getCharacter($id) : CharacterDTO 
    {
        $url = 'https://swapi.tech/api/people/' . $id;
        $response = $this->connection->getResponse($url);

        if (isset($response['error'])) {
            throw new \RuntimeException('Personagem não encontrado.', 404);
        }

        $character = $response['result'];

        if (!isset($character['properties'])) {
            throw new \RuntimeException('Dados incompletos no personagem.', 400);
        }

        return new CharacterDTO(
            $character['uid'] ?? '1',
            $character['properties']['name'] ?? 'Nome não disponível',
            $character['properties']['height'] ?? 0,
            $character['properties']['mass'] ?? 0,
            $character['properties']['hair_color'] ?? 'Cor do cabelo não disponível',
            $character['properties']['skin_color'] ?? 'Cor da pele não disponível',
            $character['properties']['eye_color'] ?? 'Cor dos olhos não disponível',
            $character['properties']['birth_year'] ?? 'Ano de nascimento não disponível',
            $character['properties']['gender'] ?? 'Gênero não disponível'
        );
    }
}