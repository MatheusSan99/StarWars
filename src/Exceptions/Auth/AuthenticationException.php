<?php

namespace StarWars\Exceptions\Auth;

use Exception;

class AuthenticationException extends Exception 
{
    const INVALID_CREDENTIALS = "Credenciais Invalidas";
    const NOT_AUTHORIZED = "Usuario Com Permissoes Insuficientes para essa acao";

    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
