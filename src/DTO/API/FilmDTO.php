<?php

namespace StarWars\DTO\API;

use JsonSerializable;
use OpenApi\Annotations as OA;

class FilmDTO implements JsonSerializable
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="Identificador único do filme",
     *     example=1
     * )
     * @var int
     */
    private int $id;

    /**
     * @OA\Property(
     *     property="title",
     *     type="string",
     *     description="Título do filme",
     *     example="Star Wars: Episode IV - A New Hope"
     * )
     * @var string
     */
    private string $title;

    /**
     * @OA\Property(
     *     property="episode_id",
     *     type="integer",
     *     description="ID do episódio do filme",
     *     example=4
     * )
     * @var int
     */
    private int $episode_id;

    /**
     * @OA\Property(
     *     property="opening_crawl",
     *     type="string",
     *     description="Abertura do filme, o famoso texto de introdução que aparece no início",
     *     example="It is a period of civil war..."
     * )
     * @var string
     */
    private string $opening_crawl;

    /**
     * @OA\Property(
     *     property="release_date",
     *     type="string",
     *     format="date",
     *     description="Data de lançamento do filme",
     *     example="1977-05-25"
     * )
     * @var string
     */
    private string $release_date;

    /**
     * @OA\Property(
     *     property="director",
     *     type="string",
     *     description="Nome do diretor do filme",
     *     example="George Lucas"
     * )
     * @var string
     */
    private string $director;

    /**
     * @OA\Property(
     *     property="producers",
     *     type="string",
     *     description="Nome dos produtores do filme",
     *     example="George Lucas, Gary Kurtz"
     * )
     * @var string
     */
    private string $producers;

    /**
     * @OA\Property(
     *     property="characters",
     *     type="array",
     *     items=@OA\Items(type="integer"),
     *     description="Lista de identificadores dos personagens que aparecem no filme",
     *     example={1, 2, 3}
     * )
     * @var array
     */
    private array $characters;

    /**
     * @OA\Property(
     *     property="isFavorite",
     *     type="boolean",
     *     description="Indica se o filme é marcado como favorito pelo usuário",
     *     example=true
     * )
     * @var bool
     */
    private bool $isFavorite = false;

    /**
     * @OA\Property(
     *     property="isOnDatabase",
     *     type="boolean",
     *     description="Indica se o filme está presente no banco de dados",
     *     example=true
     * )
     * @var bool
     */
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
