<?php

namespace CustomExceptions;

use Constants\StatusCodes;

class AuthorizationException extends ClientException
{
    function getClientMessage(): string
    {
        return "not allowed.";
    }

    function getClientCode(): int
    {
        return StatusCodes::FORBIDDEN;
    }
}