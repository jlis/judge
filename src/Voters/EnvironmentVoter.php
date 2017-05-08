<?php

namespace Jlis\Judge\Voters;

use Illuminate\Support\Facades\App;
use Jlis\Judge\Contracts\VoterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class EnvironmentVoter implements VoterInterface
{
    /**
     * @inheritdoc
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        if (empty($parameter)) {
            return false;
        }

        return App::environment($parameter);
    }
}
