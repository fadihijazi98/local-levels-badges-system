<?php
namespace Constants;

class StatusCodes extends BaseConstant
{
    // 200
    const SUCCESS = 200;
    const CREATED = 201;
    // 300
    const REDIRECT = 301;
    // 400
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const VALIDATION_ERROR = 422;
    // 500
    const INTERNAL_ERROR = 501;
}