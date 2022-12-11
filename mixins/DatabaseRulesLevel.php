<?php

namespace Mixins;

use Exception;
use CustomExceptions\ValidationException;
use Illuminate\Database\Eloquent\Model;

/**
 * Design to only used with Validator component.
 */
trait DatabaseRulesLevel
{
    /**
     * @throws Exception if `resource` not passed with values.
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_unique($value, $param, $level, $rule_details): void
    {
        if (! key_exists('resource', $rule_details)) {
            throw new Exception("the 'resource' is required with `unique` rule.");
        }
        elseif (! $param && ! key_exists('field', $rule_details)) {
            throw new Exception("the 'field' is required with `unique` rule.");
        }

        if($value === null)
        {
            return;
        }

        $field = $rule_details['field'] ?? $param;

        /**
         * @var Model $resource
         */
        $resource = $rule_details['resource'];

        $query =
            $resource::query()
                ->where($field, $value);

        if (property_exists($this, 'resource_id') && $this->resource_id)
        {
            $query->where('id', '!=', $this->resource_id);
        }

        if ($query->count() >= 1) {
            throw new ValidationException("'$value' as $param isn't unique ($level).");
        }
    }

    /**
     * Not working with parent resource!
     * @throws Exception if `resource` not passed with values.
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_exists($value, $param, $level, $rule_details): void
    {
        if (! key_exists('resource', $rule_details)) {
            throw new Exception("the 'resource' is required with `unique` rule.");
        }

        if (property_exists($this, 'resource_id') && ! $this->resource_id)
        {
            return;
        }

        /**
         * @var Model $resource
         */
        $resource = $rule_details['resource'];

        $existed =
            $resource::query()
                ->where('id', $this->resource_id)
                ->exists();


        if (! $existed) {
            throw new ValidationException("$param ($level) isn't exist.");
        }
    }

}