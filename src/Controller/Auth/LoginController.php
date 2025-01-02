<?php

declare(strict_types=1);

namespace StarWars\Controller\Auth;

use Exception;
use Nyholm\Psr7\Response;
use Psr\Log\LoggerInterface;
use StarWars\UseCases\Account\GetAccountByEmailCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Helper\FlashMessageTrait;
use StarWars\Helper\HtmlRendererTrait;
use StarWars\UseCases\API\GetCatalogCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response as Psr7Response;
use StarWars\Service\Auth\AuthService;

class LoginController
{
    use HtmlRendererTrait;
    use FlashMessageTrait;

    private LoggerInterface $logger;
    private GetAccountByEmailCase $GetAccountByEmailCase;
    private AccountLoginCase $AccountLoginCase;
    private GetCatalogCase $GetCatalogCase;
    private AuthService $AuthService;

    public function __construct(LoggerInterface $logger, GetAccountByEmailCase $GetAccountByEmailCase, AccountLoginCase $AccountLoginCase, GetCatalogCase $GetCatalogCase, AuthService $AuthService)
    {
        $this->logger = $logger;
        $this->GetAccountByEmailCase = $GetAccountByEmailCase;
        $this->AccountLoginCase = $AccountLoginCase;
        $this->GetCatalogCase = $GetCatalogCase;
        $this->AuthService = $AuthService;
    }

    public function loginForm(): ResponseInterface
    {
        if (array_key_exists('logged', $_SESSION) && $_SESSION['logged'] === true) {
            return new Response(302, [
                'Location' => '/pages/catalog'
            ]);
        }

        $html = $this->renderTemplate('Auth/login-form', [
            'titulo' => 'Login'
        ]);
        
        return new Response(200, [], $html);
    }

    public function login(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
    {
        $email = $request->getParsedBody()['email'];
        $password = $request->getParsedBody()['password'];
    
        try {
            $Account = $this->GetAccountByEmailCase->execute($email);
            $AccountModel = $this->AccountLoginCase->execute($Account->getEmail(), $password);

            $token = $this->AuthService->generateToken($AccountModel);
            $expirationTime = time() + 43200; 
            $this->AuthService->setTokenData($token, $expirationTime, $AccountModel);
    
            $this->logger->info('Usuário logado', ['email' => $email]);

            $response->getBody()->write(json_encode([
                'message' => 'Usuário logado com sucesso',
                'token' => $token,
                'expiration' => $expirationTime
            ]));

            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage(), ['email' => $email]);
            $this->addErrorMessage($e->getMessage());
            return new Response(302, [
                'Location' => '/login'
            ]);
        }
    }
}