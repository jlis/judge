<?php

namespace Jlis\Judge\Voters;

use Jlis\Judge\Feature;
use Jlis\Judge\Contracts\VoterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        return Feature::decide(
            $parameter,
            $user,
            isset($additional['defaultIfNotFound']) ? (bool) $additional['defaultIfNotFound'] : false
        );
    }
}
