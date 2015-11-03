<?php

namespace jlis\Judge\Judges;

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
        return isset($this->values[$value]);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return $this->adapter->getValues();
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function getValueRules($value)
    {
        return $this->values[$value];
    }
}
