<?php

namespace jlis\Judge\Judges;

use jlis\Judge\Adapters\AdapterInterface;
use jlis\Judge\Contracts\VoterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class Judge
{
    const RULE_PARAM_DELIMITER = ':';
    const RULE_INVERTER = '!';
    const FIELD_VALUE = 'value';
    const FIELD_FILTERS = 'filters';

    /**
     * @var AdapterInterface
     */
    protected $adapter;
    /**
     * @var VoterInterface[]
     */
    protected $voters = [];

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     * @param VoterInterface[] $voters
     */
    public function __construct(AdapterInterface $adapter, array $voters = [])
    {
        $this->adapter = $adapter;
        $this->voters = $voters;
    }

    /**
     * Decides multiple rules
     *
     * @param string|array $rules
     * @param mixed        $user
     *
     * @return bool
     */
    protected function decideRules($rules, $user = null)
    {
        if (is_string($rules)) {
            return $this->decideRule($rules, $user);
        }

        if (!is_array($rules) || empty($rules)) {
            return false;
        }

        if (isset($rules[self::FIELD_VALUE]) && !isset($rules[self::FIELD_FILTERS])) {
            return $rules[self::FIELD_VALUE];
        }

        if (array_key_exists(self::FIELD_VALUE, $rules) && array_key_exists(self::FIELD_FILTERS, $rules)) {
            if ($this->decideRules($rules[self::FIELD_FILTERS], $user)) {
                return $rules[self::FIELD_VALUE];
            }

            return false;
        }

        foreach ($rules as $subRules) {
            if (!$this->decideRule($subRules, $user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Decides a single rule
     *
     * @param string $rule
     * @param mixed  $user
     *
     * @return bool
     */
    protected function decideRule($rule, $user = null)
    {
        $parameter = null;
        $additional = [];
        $negative = false;

        if (0 === strpos($rule, self::RULE_INVERTER)) {
            $negative = true;
            $rule = substr($rule, 1);
        }

        if (false !== strpos($rule, self::RULE_PARAM_DELIMITER)) {
            $splits = explode(self::RULE_PARAM_DELIMITER, $rule);
            $rule = array_shift($splits);
            $parameter = array_shift($splits);

            if (!empty($splits)) {
                $additional = $splits;
            }
        }

        if (!isset($this->voters[strtolower($rule)])) {
            return false;
        }

        $voter = $this->voters[strtolower($rule)];
        if (!is_object($voter)) {
            if (!class_exists($voter)) {
                return false;
            }

            $voter = new $voter();
        }

        if (!$voter instanceof VoterInterface) {
            return false;
        }

        $result = $voter->vote($parameter, $user, $additional);

        return $negative ? (false === $result) : $result;
    }
}
