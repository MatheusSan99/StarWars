<?php

declare(strict_types=1);

namespace StarWars\Controller\UserRegister;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use StarWars\Helper\HtmlRendererTrait;

class LoginController
{
    use HtmlRendererTrait;

    public function loginForm(): ResponseInterface
    {
        if (array_key_exists('logado', $_SESSION) && $_SESSION['logado'] === true) {
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        return new Response(200, [], $this->renderTemplate('login'));
    }
}

