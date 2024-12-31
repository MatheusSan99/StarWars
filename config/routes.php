<?php

use StarWars\Controller\{
    Movies\MoviesController,
    Auth\LoginController,
    Auth\LogoutController,
    Account\NewAccountController,
    Error\Error404Controller,
    

};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollectorProxy;
use StarWars\Controller\Film\FilmController;
use StarWars\Controller\Movies\CatalogController;

return function (App $app) {
    $app->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response;
    });

    $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        $response->getBody()->write('Star Wars API');
        setcookie('auth_token', '', time() - 43200, '/', '', true, true); 
        session_destroy();
        
        return $response;
    });

    $app->get('/catalog', [CatalogController::class, 'getCatalog'])->add(new \StarWars\Middleware\AuthMiddleware());

    $app->get('/film/{id}', [FilmController::class, 'getFilm'])->add(new \StarWars\Middleware\AuthMiddleware());

    $app->group('', function (RouteCollectorProxy $app) {
        $app->get('/login', [LoginController::class, 'loginForm']);
        $app->post('/login', [LoginController::class, 'login']);
        $app->post('/logout', [LogoutController::class, 'logout']);
        $app->get('/create-account', [NewAccountController::class, 'createAccount']);
        $app->post('/create-account', [NewAccountController::class, 'confirmCreation']);
    });

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function () use ($app) {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse(404, 'Página não existe ou não foi encontrada');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['Erro' => 'A página solicitada não existe ou não foi encontrada.']));
        return $response;
    });
};