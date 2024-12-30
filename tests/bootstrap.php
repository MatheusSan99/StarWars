<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use StarWars\Repository\Auth\AccountRepository;

require __DIR__ . '/../vendor/autoload.php';

function getTestDatabaseConnection(): PDO
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE account (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )");

    return $pdo;
}

// Função para criar o container de dependências para testes
function createTestContainer(): ContainerInterface
{
    $containerBuilder = new ContainerBuilder();

    $containerBuilder->addDefinitions([
        PDO::class => function () {
            $pdo = getTestDatabaseConnection();
            return $pdo;
        },

        AccountRepository::class => function (ContainerInterface $c) {
            return new AccountRepository($c->get(PDO::class));
        },

    ]);

    return $containerBuilder->build();
}