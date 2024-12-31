<?php

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use StarWars\UseCases\Account\GetAccountByEmailCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Repository\Account\AccountRepository;
use StarWars\Service\Connection\ConnectionInterface;
use StarWars\Service\Connection\CurlConnection;
use StarWars\Service\StarwarsAPI\Films\FilmsAPI;
use StarWars\Service\StarwarsAPI\Films\FilmsInterface;
use StarWars\UseCases\API\GetCatalogCase;
use StarWars\UseCases\API\GetFilmCase;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $loggerSettings = $c->get('settings')['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        }
    ]);

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
        
        ConnectionInterface::class => \DI\create(CurlConnection::class),
        FilmsInterface::class => function (ContainerInterface $c) {
            return new FilmsAPI($c->get(ConnectionInterface::class));
        },

        AccountRepository::class => function (ContainerInterface $c) {
            return new AccountRepository($c->get(PDO::class));
        },

        AccountLoginCase::class => function (ContainerInterface $c) {
            return new AccountLoginCase($c->get(AccountRepository::class));
        },

        GetAccountByEmailCase::class => function (ContainerInterface $c) {
            return new GetAccountByEmailCase($c->get(AccountRepository::class));
        },

        GetCatalogCase::class => function (ContainerInterface $c) {
            return new GetCatalogCase($c->get(FilmsInterface::class));
        },

        GetFilmCase::class => function (ContainerInterface $c) {
            return new GetFilmCase($c->get(FilmsInterface::class));
        }
    ]);
    
    
};
