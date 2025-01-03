<?php

namespace StarWars\Service\Connection;

use Psr\Log\LoggerInterface;

class CurlConnection implements ConnectionInterface
{
    private array $headers = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getResponse(string $url, array $headers = []): array
    {
        $this->logger->info('Requisicao Externa: ' . $url, [
            'headers' => $headers,
            'method' => 'GET',
        ]);

        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        if (!empty($headers)) {
            $this->setHeaders($headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            $this->logger->error('Erro na Requisicao Externa: ' . $error);
            return ['error' => 'Request failed: ' . $error];
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode >= 400) {
            $this->logger->error('Erro na Requisicao Externa: ' . $httpCode);
            return ['error' => 'HTTP error: ' . $httpCode];
        }
    
        $decodedResponse = json_decode($response, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Erro na Requisicao Externa: ' . json_last_error_msg());
            return ['error' => 'JSON decode error: ' . json_last_error_msg()];
        }

        $this->logger->info('Requisicao Externa com sucesso: ' . $url, [
            'headers' => $headers,
            'method' => 'GET',
            'response' => $decodedResponse,
        ]);
    
        return $decodedResponse;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}