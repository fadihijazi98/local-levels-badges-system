<?php

namespace CustomExceptions;

use Constants\StatusCodes;

class AuthenticationException extends ClientException
{
    function getClientMessage(): string
    {
        return "not authorized";
    }

    function getClientCode(): int
    {
        return StatusCodes::UNAUTHORIZED;
    }
}