<?php

namespace StarWars\Helper;

trait FormErrorHandlerTrait
{
    public array $errors = [];

    public function validateInt(int $value, string $fieldName): ?int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false || empty($value)) {
            $this->errors[] = $fieldName . ' não informado ou inválido';
            return null;
        }
        return $value;
    }
    
    public function validateString(string $value, string $fieldName): ?string
    {
        if (filter_var($value, FILTER_SANITIZE_STRING) === false || empty($value)) {
            $this->errors[] = $fieldName . ' não informado ou inválido';
            return null;
        }
        return $value;
    }

    public function addErrorsToList(): void
    {
        foreach ($this->errors as $error) {
            $this->addErrorMessage($error);
        }
    }
}