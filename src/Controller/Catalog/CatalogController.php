<?php

namespace StarWars\Controller\Movies;

use Psr\Container\ContainerInterface;
use StarWars\DTO\API\CatalogDTO;
use StarWars\UseCases\API\GetCatalogCase;

class CatalogController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function getCatalog(): CatalogDTO
    {
        $GetCatalogCase = $this->container->get(GetCatalogCase::class);

        return $GetCatalogCase->execute();
    }
}