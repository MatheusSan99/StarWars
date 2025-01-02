<?php

namespace StarWars\Exceptions\Auth;

use Exception;

class AccountException extends Exception 
{
    const USER_NOT_FOUND = "Usuario Nao Encontrado";

    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }
    
}
