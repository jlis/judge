<?php

namespace jlis\Judge\Contracts;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
interface VoterInterface
{
    /**
     * Votes a single rule with the given parameters and user.
     *
     * @param mixed $parameter
     * @param mixed $user
     * @param array $additional
     *
     * @return bool
     */
    public function vote($parameter = null, $user = null, array $additional = []);
}
