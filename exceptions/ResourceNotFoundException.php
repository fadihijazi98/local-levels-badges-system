<?php
namespace CustomExceptions;

use Constants\StatusCodes;

class ResourceNotFoundException extends ClientException
{
    public function __construct($resource) {

        parent::__construct(
            "$resource not found."
        );
    }

    function getClientMessage(): string
    {
        return "not found";
    }

    function getClientCode(): int
    {
        return StatusCodes::NOT_FOUND;
    }
}