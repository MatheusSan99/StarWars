<?php

declare(strict_types=1);

namespace StarWars\Controller\NewAccountController;

use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StarWars\Controller\UseCases\Account\CreateNewAccountCase;
use StarWars\Helper\FlashMessageTrait;
use StarWars\Helper\HtmlRendererTrait;
use StarWars\Repository\Account\AccountRepository;

class NewAccountController
{
    use HtmlRendererTrait;
    use FlashMessageTrait;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createAccount(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, body: $this->renderTemplate('create-account'));
    }

    public function confirmCreation(ServerRequestInterface $request): ResponseInterface
    {
        $name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if (!$name || !$email || !$password) {
            $this->addErrorMessage('Todos os campos são obrigatórios');
            
            return new Response(302, ['Location' => '/create-account']);
        }

        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        $createNewAccount = new CreateNewAccountCase($this->container->get(AccountRepository::class));

        return $createNewAccount->execute($name, $email, $passwordHash);
    }
}