<?php

namespace jlis\Judge\Judges;

use Illuminate\Support\Facades\Config;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ValueJudge extends AbstractValueJudge
{
    /**
     * {@inheritDoc}
     */
    public function decide($value, $user = null, $defaultValue = false)
    {
        if (!$this->valueExists($value)) {
            return $defaultValue;
        }

        $rules = $this->getValueRules($value);
        if (!is_array($rules)) {
            return $rules;
        }

        foreach ($rules as $rule) {
            if (false !== $value = $this->decideRules($rule, $user)) {
                return $value;
            }
        }

        return $defaultValue;
    }

    /**
     * {@inheritDoc}
     */
    public function valueExists($value)
    {
        // don't use $this->values since its cached, which does not work for behat
        return isset($this->values[$value]);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return Config::get('values', []);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function getValueRules($value)
    {
        // don't use $this->values since its cached, which does not work for behat
        return $this->getValues()[$value];
    }
}
