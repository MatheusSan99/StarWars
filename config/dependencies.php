<?php

use DI\ContainerBuilder;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use StarWars\Helper\PdoLogHandler;
use StarWars\Middleware\AuthMiddleware;
use StarWars\UseCases\Account\GetAccountByEmailCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Repository\Account\AccountRepository;
use StarWars\Service\Auth\AuthService;
use StarWars\Service\Connection\ConnectionInterface;
use StarWars\Service\Connection\CurlConnection;
use StarWars\Service\StarwarsAPI\Characters\CharactersAPI;
use StarWars\Service\StarwarsAPI\Characters\CharactersInterface;
use StarWars\Service\StarwarsAPI\Films\FilmsAPI;
use StarWars\Service\StarwarsAPI\Films\FilmsInterface;
use StarWars\UseCases\API\GetCatalogCase;
use StarWars\UseCases\API\GetCharacterCase;
use StarWars\UseCases\API\GetCharactersListCase;
use StarWars\UseCases\API\GetFilmCase;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $dbPath = __DIR__ . './../database/database.sqlite';
            $dsn = 'sqlite:' . $dbPath;
    
            try {
                $pdo = new PDO($dsn);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
                $this->logger->error('Connection failed: ' . $e->getMessage());
                exit;
            }
    
            return $pdo;
        },
        LoggerInterface::class => function (ContainerInterface $c) {
            $loggerSettings = $c->get('settings')['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            // $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']); // log em arquivo
            $handler = new PdoLogHandler($c->get(PDO::class), $loggerSettings['level']); // log em banco de dados
            $logger->pushHandler($handler);

            return $logger;
        },

        ConnectionInterface::class => \DI\create(CurlConnection::class),
        FilmsInterface::class => function (ContainerInterface $c) {
            return new FilmsAPI($c->get(ConnectionInterface::class));
        },
        CharactersInterface::class => function (ContainerInterface $c) {
            return new CharactersAPI($c->get(ConnectionInterface::class));
        },

        AccountRepository::class => function (ContainerInterface $c) {
            return new AccountRepository($c->get(PDO::class));
        },

        AccountLoginCase::class => function (ContainerInterface $c) {
            return new AccountLoginCase($c->get(AccountRepository::class), $c->get(LoggerInterface::class), $c->get(AuthService::class));
        },

        GetAccountByEmailCase::class => function (ContainerInterface $c) {
            return new GetAccountByEmailCase($c->get(AccountRepository::class));
        },

        GetCatalogCase::class => function (ContainerInterface $c) {
            return new GetCatalogCase($c->get(FilmsInterface::class));
        },

        GetFilmCase::class => function (ContainerInterface $c) {
            return new GetFilmCase($c->get(FilmsInterface::class));
        },

        GetCharactersListCase::class => function (ContainerInterface $c) {
            return new GetCharactersListCase($c->get(CharactersInterface::class));
        },

        GetCharacterCase::class => function (ContainerInterface $c) {
            return new GetCharacterCase($c->get(CharactersInterface::class));
        },

        AuthMiddleware::class => function (ContainerInterface $c) {
            return new AuthMiddleware($c->get(GetAccountByEmailCase::class), $c->get(LoggerInterface::class), $c->get(AuthService::class));
        }
    ]);
};
