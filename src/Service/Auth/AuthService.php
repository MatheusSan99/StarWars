<?php

namespace StarWars\Service\Auth;

use Exception;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class AuthService
{
    private $secretKey = "https://github.com/MatheusSan99";

    public function generateToken($user)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; 
        $payload = [
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "user_id" => $user->getId(),
            "email" => $user->getEmail(),
            "role" => $user->getRole()
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token) : stdClass
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded; 
        } catch (Exception $e) {
            throw new \Exception('Token inválido: ' . $e->getMessage(), 401);
        }
    }
}
