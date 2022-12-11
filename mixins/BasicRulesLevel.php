<?php

namespace Mixins;

use CustomExceptions\ValidationException;

/**
 * Design to only used with Validator component.
 */
trait BasicRulesLevel
{
    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_required($value, $param, $level): void
    {
        if ($value === null)
        {
            throw new ValidationException("$param ($level) is required.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_integer($value, $param, $level): void
    {
        if ($value !== null && ! ctype_digit("$value"))
        {
            throw new ValidationException("$param ($level) should be an integer.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string($value, $param, $level): void
    {
        if ($value !== null && gettype($value) != "string")
        {
            throw new ValidationException("$param ($level) should be string.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string_not_empty($value, $param, $level): void
    {
        if ($value === "")
        {
            throw new ValidationException("$param ($level) should not be empty.");
        }

        $this->validate_rule_string($value, $param, $level);
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_boolean($value, $param, $level): void
    {
        if ($value !== null && gettype($value) != "boolean" &&
            ! in_array("$value", ["true", "True", "false", "False"]))
        {
            throw new ValidationException("$param ($level) should be boolean.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_email($value, $param, $level): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("$param ($level) invalid email format.");
        }

    }

}