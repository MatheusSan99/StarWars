<?php

declare(strict_types=1);

namespace StarWars\Controller\Account;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use StarWars\Controller\UseCases\Account\CreateNewAccountCase;
use StarWars\Helper\FlashMessageTrait;
use StarWars\Helper\HtmlRendererTrait;
use StarWars\Repository\Account\AccountRepository;

class NewAccountController
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

    public function createAccount(ServerRequestInterface $request): ResponseInterface
    {
        $html = $this->renderTemplate('Auth/create-account', [
            'titulo' => 'Criar Conta'
        ]);

        return new Response(200, [], $html);
    }

    public function confirmCreation(ServerRequestInterface $request): ResponseInterface
    {
        $name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if (!$name || !$email || !$password) {
            $this->logger->warning('Todos os campos são obrigatórios', ['email' => $email, 'name' => $name]);
            $this->addErrorMessage('Todos os campos são obrigatórios');
            
            return new Response(302, ['Location' => '/create-account']);
        }

        $createNewAccount = new CreateNewAccountCase($this->container->get(AccountRepository::class));

        try {
            $createNewAccount->execute($name, $email, $password);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao criar conta: ' . $e->getMessage(), ['email' => $email, 'name' => $name]);

            $this->addErrorMessage('Erro ao criar conta, tente novamente com um email diferente');
            
            return new Response(302, ['Location' => '/create-account']);
        }

        $this->addSuccessMessage('Conta criada com sucesso, redirecionando para a página de login');

        return new Response(302, ['Location' => '/login']);
    }
}