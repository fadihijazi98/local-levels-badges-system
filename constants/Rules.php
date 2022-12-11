<?php

namespace Constants;

class Rules extends BaseConstant
{
    const REQUIRED = 'required';
    const NOT_EMPTY = 'string_not_empty';
    const INTEGER = 'integer';
    const STRING = 'string';
    const BOOLEAN = 'boolean';
    const EMAIL = 'email';
    const UNIQUE = 'unique';
    const EXISTS = 'exists';
    const MAX = 'MAX';
    const MIN = 'MIN';
    const LENGTH = 'length';
}