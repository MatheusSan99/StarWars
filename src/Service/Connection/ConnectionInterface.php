<?php

namespace StarWars\Service\Connection;

interface ConnectionInterface
{
    public function getResponse(string $url): array;
    public function setHeaders(array $headers): void;
}