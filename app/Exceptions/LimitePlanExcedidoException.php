<?php

namespace App\Exceptions;

use Exception;

class LimitePlanExcedidoException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $limite = 0,
        public readonly int $usados = 0,
        public readonly string $recurso = 'plan',
    ) {
        parent::__construct($message);
    }

    public function toResponse(): array
    {
        return [
            'error' => $this->getMessage(),
            'recurso' => $this->recurso,
            'limite' => $this->limite,
            'usados' => $this->usados,
        ];
    }
}
