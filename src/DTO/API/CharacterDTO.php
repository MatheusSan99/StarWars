<?php

namespace StarWars\DTO\API;

class CharacterDTO implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $height;
    private string $mass;
    private string $hair_color;
    private string $skin_color;
    private string $eye_color;
    private string $birth_year;
    private string $gender;

    public function __construct(
        int $id,
        string $name,
        string $height,
        string $mass,
        string $hair_color,
        string $skin_color,
        string $eye_color,
        string $birth_year,
        string $gender
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->height = $height;
        $this->mass = $mass;
        $this->hair_color = $hair_color;
        $this->skin_color = $skin_color;
        $this->eye_color = $eye_color;
        $this->birth_year = $birth_year;
        $this->gender = $gender;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function getMass(): string
    {
        return $this->mass;
    }

    public function getHairColor(): string
    {
        return $this->hair_color;
    }

    public function getSkinColor(): string
    {
        return $this->skin_color;
    }

    public function getEyeColor(): string
    {
        return $this->eye_color;
    }

    public function getBirthYear(): string
    {
        return $this->birth_year;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCover(): string
    {
        return '../../../public/img/characters/' . $this->getId() . '.jpg';
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'height' => $this->getHeight(),
            'mass' => $this->getMass(),
            'hair_color' => $this->getHairColor(),
            'skin_color' => $this->getSkinColor(),
            'eye_color' => $this->getEyeColor(),
            'birth_year' => $this->getBirthYear(),
            'gender' => $this->getGender(),
            'cover' => $this->getCover()
        ];
    }
}
