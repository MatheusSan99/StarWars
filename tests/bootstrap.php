<?php

use DI\ContainerBuilder;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use StarWars\Helper\PdoLogHandler;
use StarWars\Repository\Account\AccountRepository;

require __DIR__ . '/../vendor/autoload.php';

function getTestDatabaseConnection(): PDO
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE accounts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE monolog (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        channel TEXT NOT NULL,
        level INTEGER NOT NULL,
        message TEXT NOT NULL,
        time INTEGER NOT NULL
    )");

    return $pdo;
}

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

        'settings' => [
            'logger' => [
                'name' => 'test_logger',
                'level' => Logger::DEBUG,
                'path' => 'php://stdout',
            ],
        ],
        
        LoggerInterface::class => function (ContainerInterface $c) {
            $loggerSettings = $c->get('settings')['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new PdoLogHandler($c->get(PDO::class), $loggerSettings['level']); // log em banco de dados
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);

    return $containerBuilder->build();
}

