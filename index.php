<?php

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

require __DIR__ . '/vendor/autoload.php';

session_start();

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/config/configs.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/config/dependencies.php';
$dependencies($containerBuilder);

// Create a Container using PHP-DI to manage containers (containers are like little snipets or functions returning something ready to all the app life)
$container = $containerBuilder->build();

// Add container to AppFactory before create App
AppFactory::setContainer($container);


// Instantiate App
$app = AppFactory::create();

// Setup a supersimple auth checker, intercepting http calls with this middleware and checking that only allowed routes can be navigated without auth
$loggedInMiddleware = function ($request, $handler): ResponseInterface {
    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();

    if (empty($route)) {
        throw new HttpNotFoundException($request, $response);
    }

    $routeName = $route->getName();

    $publicRoutesArray = array('', 'login');

    if (empty($_SESSION['user']) && (!in_array($routeName, $publicRoutesArray))) {
        $routeParser = $routeContext->getRouteParser();
        $url = $routeParser->urlFor('login');

        $response = new \Slim\Psr7\Response();

        return $response->withStatus(401);
        // return $response->withHeader('Location', $url)->withStatus(302);
    } else {
        $response = $handler->handle($request);

        return $response;
    }
};
$app->add($loggedInMiddleware);
$app->addRoutingMiddleware();

$errorSetting = true; //$app->getContainer()->get('settings')['displayErrorDetails'];
$app->addErrorMiddleware($errorSetting, true, true);

// Add routes
$routes = require __DIR__ . '/config/routes.php';
$routes($app);

$app->run();
