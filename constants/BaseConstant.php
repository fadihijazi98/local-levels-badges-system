<?php

namespace Constants;

use ReflectionClass;

class BaseConstant
{
    public function list(): array
    {
        return (new ReflectionClass($this))->getConstants();
    }
}