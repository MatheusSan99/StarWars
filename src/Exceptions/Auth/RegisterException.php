<?php

namespace StarWars\Exceptions\Auth;

use Exception;

class RegisterException extends Exception 
{
    const INVALID_CREDENTIALS = "Credenciais Invalidas";
    const INVALID_EMAIL = "Email Invalido";
    const NAME_TOO_SHORT = "Nome Muito Curto";
    const REQUIRED_FIELDS = "Todos os campos são obrigatórios";
    const EMAIL_ALREADY_EXISTS = "Email já cadastrado, tente outro";
    const UNKNOW_ERROR = "Erro desconhecido ao criar conta: ";

    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
