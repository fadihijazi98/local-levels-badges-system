<?php

namespace Components;

use Constants\Rules;
use Exception;
use CustomExceptions\ValidationException;
use Mixins\BasicRulesLevel;
use Mixins\DatabaseRulesLevel;

class Validator
{
    use BasicRulesLevel, DatabaseRulesLevel;
    /**
     * each rule in the `$rules` array has corresponding method in component starts with `validate_rule_`.
     * the rules in component are scalable. you can make your rule by add it in the array,
     * then create the logic in the corresponding method starts with `validate_rule_`.
     * @var string[] $rules
     */
    private array $rules;

    private $resource_id;

    public function __construct()
    {
        $this->rules = (new Rules())->list();
    }

    /**
     * @throws Exception if rule hasn't implementation.
     */
    private function validateIfRuleIsExists($rule): void
    {
        $rule = $this->handleRuleCallConvention($rule);

        if (! method_exists($this, $rule))
        {
            throw new Exception("rule $rule hasn't implementation.");
        }

    }

    /**
     * Standard convention is -> validate_rule_.*
     */
    private function handleRuleCallConvention($rule): string
    {
        $convention = explode('_', $rule);

        if (! in_array('validate', $convention))
        {
            $convention = ['validate', ...$convention];
        }

        if (! in_array('rule', $convention))
        {
            $convention = [$convention[0], 'rule', ...array_slice($convention, 1)];
        }


        return join("_", $convention);
    }

    /**
     * @throws Exception if there is unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    private function validate($schema, $values, $level, $values_are_positional=false): void
    {
        foreach ($schema as $key => $rules)
        {
            if (! is_array($rules))
            {
                throw new Exception("`$key` validation should be array of rules.");
            }

            foreach ($rules as $index => $rule)
            {
                /**
                 * handle special rule like 'unique'
                 * when rule has extra details
                 * the rule will be stored as associated array
                 * then the key will be the rule
                 * the value will be the rule details
                 */
                if (! is_numeric($index))
                {
                    $rule_details = $rule;
                    $rule = $index;
                }

                if(! in_array($rule, $this->rules))
                {
                    throw new Exception("this rule ($rule) isn't listed in constants.Rules.");
                }

                $rule = $this->handleRuleCallConvention($rule);
                $this->validateIfRuleIsExists($rule);

                if ($values_are_positional) {
                    $value = array_shift($values);
                }
                else {
                    $value = $values[$key] ?? null;
                }

                $arguments = [$value, $key, $level];

                if (isset($rule_details))
                {
                    $arguments[] = $rule_details;
                }

                $this->$rule(...$arguments);
            }
        }

    }

    /**
     * rules in `$urlParamsValidationSchema` should be ordered according the passed `$url_params` to handle correct.
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateUrlParams(array $validationSchema, array $values): void
    {
        if ($values) {
            $this->resource_id = end($values);
        }

        $this->validate($validationSchema, $values, "url params", true);
    }

    /**
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateQueryParams(array $validationSchema, array $values, $resource_id=null): void
    {
        if ($resource_id) {
            $this->resource_id = $resource_id;
        }

        $this->validate($validationSchema, $values, "query params");
    }

    /**
     * @throws Exception if they are unimplemented rule
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateRequestPayload(array $validationSchema, array $values, $resource_id=null): void
    {
        if ($resource_id) {
            $this->resource_id = $resource_id;
        }

        $this->validate($validationSchema, $values, "request payload");
    }

}