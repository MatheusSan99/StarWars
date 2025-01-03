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
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="StarWars API Documentation", version="0.1")
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Servidor local para desenvolvimento"
 * )
 */

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



    public function loginPage(): ResponseInterface
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

/**
 * @OA\Post(
 *     path="/api/internal/login",
 *     tags={"Auth"},
 *     summary="Realiza o login do usuário",
 *     description="Autentica o usuário com base no email e senha fornecidos.",
 *     @OA\RequestBody(
 *         required=true,
 *         content={
 *             @OA\MediaType(
 *                 mediaType="multipart/form-data",
 *                 @OA\Schema(
 *                     required={"email", "password"},
 *                     @OA\Property(
 *                         property="email",
 *                         type="string",
 *                         format="email",
 *                         description="Endereço de e-mail do usuário"
 *                     ),
 *                     @OA\Property(
 *                         property="password",
 *                         type="string",
 *                         format="password",
 *                         description="Senha do usuário"
 *                     )
 *                 )
 *             )
 *         }
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login bem-sucedido",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Usuário logado com sucesso"
 *             ),
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 description="Token JWT para autenticação"
 *             ),
 *             @OA\Property(
 *                 property="expiration",
 *                 type="integer",
 *                 description="Timestamp de expiração do token"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciais inválidas",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Email ou senha incorretos"
 *             )
 *         )
 *     )
 * )
 */


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
