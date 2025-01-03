<?php

namespace StarWars\Service\Auth;

use Exception;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;
use StarWars\Model\Account\AccountModel;
use stdClass;

class AuthService
{
    private $secretKey = "https://github.com/MatheusSan99";
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

        $this->logger->info('Token gerado com sucesso para o usuário: ' . $user->getEmail());

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token) : stdClass
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));

            $this->logger->info('Token validado com sucesso.');

            return $decoded; 
        } catch (Exception $e) {
            $this->logger->error('Token inválido: ' . $e->getMessage());
            throw new \Exception('Token inválido: ' . $e->getMessage(), 401);
        }
    }

    public function logout() : void
    {
        $this->logger->info('Usuário deslogado.');
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

        $this->logger->info('Dados do token definidos com sucesso.');
    }
}
