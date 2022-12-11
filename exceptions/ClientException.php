<?php

namespace CustomExceptions;

abstract class ClientException extends \Exception
{
    abstract function getClientMessage(): string;

    abstract function getClientCode(): int;

    public function __construct(string $message = "")
    {
        parent::__construct(
            $message ?? $this->getClientMessage(),
            $this->getClientCode()
        );
    }
}