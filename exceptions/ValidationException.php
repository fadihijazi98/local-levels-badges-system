<?php
namespace CustomExceptions;

use Constants\StatusCodes;

class ValidationException extends ClientException
{
    function getClientMessage(): string
    {
        return "sent data isn't valid";
    }

    function getClientCode(): int
    {
        return StatusCodes::VALIDATION_ERROR;
    }
}