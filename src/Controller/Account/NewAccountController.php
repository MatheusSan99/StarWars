<?php

declare(strict_types=1);

namespace StarWars\Controller\Account;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response as Psr7Response;
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
 *     path="/api/internal/create-account",
 *     summary="Confirma a criação de uma nova conta",
 *     description="Este endpoint recebe os dados do usuário (nome, e-mail, senha) e cria uma nova conta.",
 *     operationId="confirmCreation",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name", "email", "password"},
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", format="email", example="joao.silva@email.com"),
 *                 @OA\Property(property="password", type="string", example="password123")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Conta criada com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Usuário registrado com sucesso"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Campos obrigatórios ausentes ou inválidos, como nome, e-mail ou senha.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Campos obrigatórios ausentes ou inválidos."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro desconhecido ao tentar criar a conta.",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Erro desconhecido ao tentar criar a conta."
 *             )
 *         )
 *     )
 * )
 */

 public function confirmCreation(ServerRequestInterface $request, Psr7Response $response, array $args): ResponseInterface
 {
     $name = $request->getParsedBody()['name'];
     $email = $request->getParsedBody()['email'];
     $password = $request->getParsedBody()['password'];
 
     if (!$name || !$email || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $this->logger->warning(RegisterException::REQUIRED_FIELDS, ['email' => $email, 'name' => $name]);
         $this->addErrorMessage(RegisterException::REQUIRED_FIELDS);
 
         $response->getBody()->write(json_encode([
             'message' => RegisterException::REQUIRED_FIELDS
         ]));
 
         return $response->withHeader('Content-Type', 'application/json');
     }
 
     try {
         $this->CreateNewAccount->execute($name, $email, $password);
         $this->logger->info('Conta criada com sucesso', ['email' => $email, 'name' => $name]);
         $this->addSuccessMessage('Conta criada com sucesso, redirecionando para a página de login');
 
         $response->getBody()->write(json_encode([
             'message' => 'Usuário registrado com sucesso'
         ]));
 
         return $response->withHeader('Content-Type', 'application/json');
     } catch (\Exception $e) {
         $this->logger->error(RegisterException::UNKNOW_ERROR . $e->getMessage(), ['email' => $email, 'name' => $name]);
         $this->addErrorMessage(RegisterException::UNKNOW_ERROR . $e->getMessage());
 
         $response->getBody()->write(json_encode([
             'message' => RegisterException::UNKNOW_ERROR . $e->getMessage()
         ]));
 
         return $response->withHeader('Content-Type', 'application/json');
     }
 }
 
}
