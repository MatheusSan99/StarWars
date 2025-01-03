<?php

declare(strict_types=1);

namespace StarWars\Controller\Account;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use StarWars\Exceptions\Auth\RegisterException;
use StarWars\UseCases\Account\CreateNewAccountCase;
use StarWars\Helper\FlashMessageTrait;
use StarWars\Helper\HtmlRendererTrait;

class NewAccountController
{
    use HtmlRendererTrait;
    use FlashMessageTrait;

    private CreateNewAccountCase $CreateNewAccount;
    private LoggerInterface $logger;

    public function __construct(CreateNewAccountCase $CreateNewAccount, LoggerInterface $logger)
    {
        $this->CreateNewAccount = $CreateNewAccount;
        $this->logger = $logger;
    }

    public function createAccountPage(ServerRequestInterface $request): ResponseInterface
    {
        $html = $this->renderTemplate('Auth/create-account', [
            'titulo' => 'Criar Conta'
        ]);

        return new Response(200, [], $html);
    }

    /**
     * @OA\Post(
     *     path="/api/internal/confirm-creation",
     *     summary="Confirma a criação de uma nova conta",
     *     description="Este endpoint recebe os dados do usuário (nome, e-mail, senha) e cria uma nova conta.",
     *     operationId="confirmCreation",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirecionamento para a página de login após conta criada com sucesso.",
     *         @OA\Header(
     *             header="Location",
     *             description="URL de redirecionamento",
     *             @OA\Schema(type="string", example="/pages/login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro devido a campos obrigatórios ausentes ou inválidos.",
     *         @OA\Header(
     *             header="Location",
     *             description="URL de redirecionamento para a criação de conta.",
     *             @OA\Schema(type="string", example="/pages/create-account")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro desconhecido ao tentar criar a conta.",
     *         @OA\Header(
     *             header="Location",
     *             description="URL de redirecionamento para a criação de conta.",
     *             @OA\Schema(type="string", example="/pages/create-account")
     *         )
     *     ),
     *     security={{
     *         "bearerAuth": {}
     *     }}
     * )
     */
    public function confirmCreation(ServerRequestInterface $request): ResponseInterface
    {
        $name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if (!$name || !$email || !$password) {
            $this->logger->warning(RegisterException::REQUIRED_FIELDS, ['email' => $email, 'name' => $name]);
            $this->addErrorMessage(RegisterException::REQUIRED_FIELDS);

            return new Response(302, ['Location' => '/pages/create-account']);
        }

        try {
            $this->CreateNewAccount->execute($name, $email, $password);
        } catch (\Exception $e) {
            $this->logger->error(RegisterException::UNKNOW_ERROR . $e->getMessage(), ['email' => $email, 'name' => $name]);

            $this->addErrorMessage(RegisterException::UNKNOW_ERROR . $e->getMessage());

            return new Response(302, ['Location' => '/pages/create-account']);
        }

        $this->logger->info('Conta criada com sucesso', ['email' => $email, 'name' => $name]);
        $this->addSuccessMessage('Conta criada com sucesso, redirecionando para a página de login');

        return new Response(302, ['Location' => '/pages/login']);
    }
}
