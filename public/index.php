<?php

declare(strict_types=1);

use StarWars\Controller\Error\Error404Controller;

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../vendor/autoload.php';
$routes = require_once __DIR__ . '/../config/routes.php';
/** @var \Psr\Container\ContainerInterface $diContainer */
$diContainer = require_once __DIR__ . '/../config/dependencies.php'; 
$containerBuilder = new \DI\ContainerBuilder();  

$diContainer($containerBuilder); 
$diContainer = $containerBuilder->build();
$pathInfo = $_SERVER['PATH_INFO'] ?? '/';
$httpMethod = $_SERVER['REQUEST_METHOD'];
session_start();
session_regenerate_id();
$isLoginRoute = $pathInfo === '/pages/login';
$isCreateAccountRoute = $pathInfo === '/pages/create-account';

if (!array_key_exists('logged', $_SESSION) && !$isLoginRoute && !$isCreateAccountRoute) {
    header('Location: /pages/login');
    return;
}

$key = "$httpMethod|$pathInfo";

if (property_exists($routes, $key)) {
    [$controllerClass, $method] = $routes[$key];
    $controller = $diContainer->get($controllerClass);

    if (!method_exists($controller, $method)) {
        $controller = $diContainer->get(Error404Controller::class);
        $method = 'handle';
    }
} else {
    $controller = $diContainer->get(Error404Controller::class);

    $method = 'handle';
}

$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

$creator = new \Nyholm\Psr7Server\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory,  // StreamFactory
);

$request = $creator->fromGlobals();

if (method_exists($controller, $method)) {
    $response = $controller->$method($request);
} else {
    $response = (new Error404Controller())->handle($request);
}

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
