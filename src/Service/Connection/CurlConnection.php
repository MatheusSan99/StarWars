<?php

namespace StarWars\Service\Connection;

class CurlConnection implements ConnectionInterface
{
    private array $headers = [];

    public function getResponse(string $url, array $headers = []): array
    {
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
            return ['error' => 'Request failed: ' . $error];
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode >= 400) {
            return ['error' => 'HTTP error: ' . $httpCode];
        }
    
        $decodedResponse = json_decode($response, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON decode error: ' . json_last_error_msg()];
        }
    
        return $decodedResponse;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}