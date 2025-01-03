<?php

namespace StarWars\DTO\API;

use JsonSerializable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FilmDTO",
 *     type="object",
 *     required={"id", "title", "episode_id", "opening_crawl", "release_date", "director", "producers", "characters"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="A New Hope"),
 *     @OA\Property(property="episode_id", type="integer", example=4),
 *     @OA\Property(property="opening_crawl", type="string", example="It is a period of civil war. Rebel spaceships, striking from a hidden base, have won their first victory against the evil Galactic Empire."),
 *     @OA\Property(property="release_date", type="string", format="date", example="1977-05-25"),
 *     @OA\Property(property="director", type="string", example="George Lucas"),
 *     @OA\Property(property="producers", type="string", example="Gary Kurtz, Rick McCallum"),
 *     @OA\Property(property="characters", type="array", @OA\Items(type="integer", example=1)),
 *     @OA\Property(property="isFavorite", type="boolean", example=false),
 *     @OA\Property(property="isOnDatabase", type="boolean", example=true)
 * )
 */

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

    public function ageInYears(): int
    {
        $releaseDate = new \DateTime($this->getReleaseDate());
        $currentDate = new \DateTime();
        $diff = $currentDate->diff($releaseDate);
        return $diff->y;
    }

    public function ageInMonths(): int
    {
        $releaseDate = new \DateTime($this->getReleaseDate());
        $currentDate = new \DateTime();
        $diff = $currentDate->diff($releaseDate);

        return ($diff->y * 12) + $diff->m;
    }

    public function ageInDays(): int
    {
        $releaseDate = new \DateTime($this->getReleaseDate());
        $currentDate = new \DateTime();
        $interval = $releaseDate->diff($currentDate);

        return (int)$releaseDate->diff($currentDate)->format('%a');
    }

    public function completeAge(): string
    {
        $releaseDate = new \DateTime($this->getReleaseDate());

        $currentDate = new \DateTime();

        $diff = $currentDate->diff($releaseDate);

        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        return "{$years} anos, {$months} meses e {$days} dias";
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
            'isOnDatabase' => $this->isOnDatabase(),
            'complete_age' => $this->completeAge(),
            'age_in_years' => $this->ageInYears(),
            'age_in_months' => $this->ageInMonths(),
            'age_in_days' => $this->ageInDays()
        ];
    }
}
