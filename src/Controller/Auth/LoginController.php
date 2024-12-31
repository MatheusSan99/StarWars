<?php

declare(strict_types=1);

namespace StarWars\Controller\Auth;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use StarWars\UseCases\Account\GetAccountByEmailCase;
use StarWars\UseCases\Auth\AccountLoginCase;
use StarWars\Helper\FlashMessageTrait;
use StarWars\Helper\HtmlRendererTrait;
use StarWars\UseCases\API\GetCatalogCase;

class LoginController
{
    use HtmlRendererTrait;
    use FlashMessageTrait;

    private ContainerInterface $container;
    private LoggerInterface $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function loginForm(): ResponseInterface
    {
        if (array_key_exists('logged', $_SESSION) && $_SESSION['logged'] === true) {
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        $html = $this->renderTemplate('Auth/login-form', [
            'titulo' => 'Login'
        ]);
        
        return new Response(200, [], $html);
    }

    public function login(): ResponseInterface
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
        if ($email === false || $password === false) {
            $this->logger->warning('Dados inválidos durante o login', ['email' => $email]);
            $this->addErrorMessage('Email ou senha inválidos');

            return new Response(302, [
                'Location' => '/login'
            ]);
        }
    
        $AccountByEmail = $this->container->get(GetAccountByEmailCase::class);
        $loginCase = $this->container->get(AccountLoginCase::class);

        try {
            $Account = $AccountByEmail->execute($email);

            $token = $loginCase->execute($Account->getEmail(), $password);

            $_SESSION['logged'] = true;
    
            $expirationTime = time() + 43200; 
    
            setcookie(
                'auth_token',     
                $token,         
                $expirationTime,  
                '/',            
                '',               
                true,             
                true             
            );
    
            $this->logger->info('Usuário logado', ['email' => $email]);

            $GetCatalogCase = $this->container->get(GetCatalogCase::class);

            $Catalog = $GetCatalogCase->execute();
            $html = $this->renderTemplate('Catalog/movies-list', [
                'titulo' => 'Catalogo de Filmes',
                'catalog' => $Catalog 
            ]);
            
            return new Response(200, [], $html);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Usuário não encontrado', ['email' => $email]);
            $this->addErrorMessage('Email ou senha inválidos');
            return new Response(302, [
                'Location' => '/login'
            ]);
        }
    }

}

