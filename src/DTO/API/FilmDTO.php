<?php

namespace StarWars\DTO\API;

use JsonSerializable;

class FilmDTO implements JsonSerializable
{
    private int $id;
    private string $title;
    private int $episode_id;
    private string $opening_crawl;
    private string $release_date;
    private string $director;
    private string $producers;
    private array $characters;
    private bool $isFavorite = false;
    private bool $isOnDatabase = false;

    public function __construct(
        int $id,
        string $title,
        int $episode_id,
        string $opening_crawl,
        string $release_date,
        string $director,
        string $producers,
        array $characters,
        bool $isFavorite = false,
        string $filmImage = '',
        bool $isOnDatabase = false
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->episode_id = $episode_id;
        $this->opening_crawl = $opening_crawl;
        $this->release_date = $release_date;
        $this->director = $director;
        $this->producers = $producers;
        $this->setCharacters($characters);
        $this->isFavorite = $isFavorite;
    }

    private function setCharacters(array $characters): void
    {
        $this->characters = array_map(function ($character) {
            return (int)str_replace('https://www.swapi.tech/api/people/', '', $character);
        }, $characters);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getEpisodeId(): int
    {
        return $this->episode_id;
    }

    public function getOpeningCrawl(): string
    {
        return $this->opening_crawl;
    }

    public function getReleaseDate(): string
    {
        return $this->release_date;
    }

    public function getDirector(): string
    {
        return $this->director;
    }

    public function getProducers(): string
    {
        return $this->producers;
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }

    public function isOnDatabase(): bool
    {
        return $this->isOnDatabase;
    }

    public function getCover(): string
    {
        return '../../public/img/films/' . $this->getId() . '.jpg';
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'episode_id' => $this->getEpisodeId(),
            'opening_crawl' => $this->getOpeningCrawl(),
            'release_date' => $this->getReleaseDate(),
            'director' => $this->getDirector(),
            'producers' => $this->getProducers(),
            'characters' => $this->getCharacters(),
            'isFavorite' => $this->isFavorite(),
            'cover' => $this->getCover(),
            'isOnDatabase' => $this->isOnDatabase()
        ];
    }
}