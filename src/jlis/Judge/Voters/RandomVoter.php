<?php

namespace jlis\Judge\Voters;

use jlis\Judge\Contracts\VoterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class RandomVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        if (empty($parameter)) {
            return false;
        }

        return mt_rand(0, 100) <= (int) $parameter;
    }
}
