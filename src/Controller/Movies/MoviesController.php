<?php

namespace StarWars\Controller\Movies;

class MoviesController
{
    public function getMovies($request, $response, $args)
    {
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['Filmes' => 'Filmes']));
        return $response;
    }
}