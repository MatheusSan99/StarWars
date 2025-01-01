<?php

use StarWars\Controller\{
    Auth\LoginController,
    Auth\LogoutController,
    Account\NewAccountController,
    Catalog\CatalogController,
    Film\FilmController,
    Characters\CharactersController
};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollectorProxy;
use StarWars\Middleware\AuthMiddleware;

return function (App $app) {
    $app->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response;
    });

    $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response->withHeader('Location', '/pages/login')->withStatus(302);
    });

    $app->group('/pages', function (RouteCollectorProxy $app) {
        $app->get('/login', LoginController::class . ':loginForm');
        $app->get('/create-account', [NewAccountController::class, 'createAccount']);
        $app->get('/catalog', CatalogController::class . ':catalogPage')->add($app->getContainer()->get(AuthMiddleware::class));

        $app->group('/film', function (RouteCollectorProxy $app) {
            $app->get('', FilmController::class . ':getFilmPage');
            $app->get('/{filmId}/characters', CharactersController::class . ':getCharactersPage');
            $app->get('/{filmId}/characters/{characterId}', CharactersController::class . ':getCharacterPage');
        })->add($app->getContainer()->get(AuthMiddleware::class));

    });

    $app->group('/api', function (RouteCollectorProxy $app) {
        $app->group('/internal', function (RouteCollectorProxy $app) {
            $app->post('/login', LoginController::class . ':login');
            $app->post('/logout', LogoutController::class . ':logout');
            $app->post('/create-account', [NewAccountController::class, 'confirmCreation']);
        });
    
        $app->group('/external', function (RouteCollectorProxy $app) {
            $app->get('/catalog', CatalogController::class . ':getCatalog'); 
            $app->get('/film/{id}', FilmController::class . ':getFilmById');
            $app->get('/film/{filmId}/characters', CharactersController::class . ':getCharactersByFilmId');  
            $app->get('/film/{filmId}/characters/{characterId}', CharactersController::class . ':getCharacterById');
        });
    });

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function () use ($app) {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse(404, 'Página não existe ou não foi encontrada');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['error' => 'A página solicitada não existe ou não foi encontrada.']));
        return $response;
    });
};