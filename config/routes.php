<?php

use API\CheckPrice\Controller\{
    GasStationController,
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
        
        ob_start(); 
        phpinfo(); 
        $phpinfo = ob_get_clean(); 
        
        $response->getBody()->write($phpinfo);
        
        return $response;
    });
    

    $app->get('/v1/price/gasoline/{month}/{year}', GasStationController::class . ':checkActualPrice');

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function () use ($app) {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse(404, 'Página não existe ou não foi encontrada');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['Erro' => 'A página solicitada não existe ou não foi encontrada.']));
        return $response;
    });
};
