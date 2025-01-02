<?php

namespace StarWars\Service\Auth;

use Exception;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use StarWars\Model\Account\AccountModel;
use stdClass;

class AuthService
{
    private $secretKey = "https://github.com/MatheusSan99";

    public function generateToken(AccountModel $user) : string
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

    public function logout() : void
    {
        setcookie('auth_token', '', time() - 43200, '/', '', true, true); 
        session_destroy();
    }

    public function setTokenData(string $token, int $expirationTime, AccountModel $user) : void
    {
        setcookie(
            'auth_token',     
            $token,         
            $expirationTime,  
            '/',            
            '',               
            true,             
            true             
        );
        $_SESSION['logged'] = true;
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['user_email'] = $user->getEmail();
    }
}
