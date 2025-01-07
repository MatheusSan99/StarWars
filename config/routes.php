<?php

use Nyholm\Psr7\Response;
use StarWars\Controller\{
    Auth\LoginController,
    Auth\LogoutController,
    Account\NewAccountController,
    Catalog\CatalogController,
    Film\FilmController,
    Characters\CharactersController,
    Doc\DocumentationController
};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use StarWars\Middleware\AuthMiddleware;
use StarWars\Middleware\CacheMiddleware;

return function (App $app) {
    $app->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response;
    });

    $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response->withHeader('Location', '/pages/login')->withStatus(302);
    });

    $app->group('/pages', function (RouteCollectorProxy $app) {
        $app->get('/login', LoginController::class . ':loginPage');
        $app->get('/create-account', NewAccountController::class . ':createAccountPage');
        $app->get('/catalog', CatalogController::class . ':catalogPage')->add($app->getContainer()->get(AuthMiddleware::class));
        $app->get('/documentation', DocumentationController::class . ':docPage')->add($app->getContainer()->get(AuthMiddleware::class));

        $app->group('/film', function (RouteCollectorProxy $app) {
            $app->get('', FilmController::class . ':getFilmPage');
            $app->get('/{filmId}/characters', CharactersController::class . ':getCharactersPage');
        })->add($app->getContainer()->get(AuthMiddleware::class));
    });

    $app->group('/api', function (RouteCollectorProxy $app) {
        $app->group('/internal', function (RouteCollectorProxy $app) {
            $app->get('/documentation', DocumentationController::class . ':getApiDoc');
            $app->post('/login', LoginController::class . ':login');
            $app->post('/logout', LogoutController::class . ':logout');
            $app->post('/create-account', NewAccountController::class . ':confirmCreation');
        });
    
        $app->group('/external', function (RouteCollectorProxy $app) {
            $app->get('/catalog', CatalogController::class . ':getCatalog'); 
            $app->get('/film/{id}', FilmController::class . ':getFilmById');
            $app->get('/film/{filmId}/characters', CharactersController::class . ':getCharactersByFilmId');  
        });
    });

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/gtf', function () use ($app) {
        ob_start();
        require_once __DIR__ . '/../src/View/gtf.php';
        return new Response(403, [], ob_get_clean());
    });

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function () use ($app) {
        ob_start();
        require_once __DIR__ . '/../src/View/Error/not-found.php';
        return new Response(404, [], ob_get_clean());
    });
};
