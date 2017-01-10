<?php

namespace jlis\Judge\Voters;

use jlis\Judge\Value;
use jlis\Judge\Contracts\VoterInterface;

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
