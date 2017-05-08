<?php

namespace Jlis\Judge\Voters;

use Illuminate\Support\Facades\Config;
use Jlis\Judge\Contracts\VoterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class DebugVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        if (empty($parameter)) {
            return false;
        }

        return Config::get('app.debug') === (bool) $parameter;
    }
}
