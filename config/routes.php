<?php

use StarWars\Controller\{
    Movies\MoviesController,
    UserRegister\NewAccountController,
    UserRegister\LoginController,
    UserRegister\LogoutController,
    Error\Error404Controller,
    

};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;

return function (App $app) {
    $app->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $response;
    });

    $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
        $response = $response->withHeader('Content-Type', 'text/html');
 
        # write php ini on the response
        
        ob_start();

        xdebug_info();

        $phpinfo = ob_get_clean();

        $response->getBody()->write($phpinfo);

        return $response;
    });

    $app->get('/movies', [MoviesController::class, 'getMovies'])->add(new \StarWars\Middleware\AuthMiddleware());
    $app->get('/login', [LoginController::class, 'login']);

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function () use ($app) {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse(404, 'Página não existe ou não foi encontrada');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['Erro' => 'A página solicitada não existe ou não foi encontrada.']));
        return $response;
    });
};