<?php

namespace jlis\Judge\Voters;

use jlis\Judge\Contracts\VoterInterface;
use jlis\Judge\Value;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ValueVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        return Value::decide(
            $parameter,
            $user,
            isset($additional['defaultValue']) ? $additional['defaultValue'] : false
        );
    }
}
