<?php

namespace jlis\Judge\Voters;

use jlis\Judge\Contracts\VoterInterface;
use jlis\Judge\Judges\FeatureJudge;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ExpressionLanguageVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote($parameter = null, $user = null, array $additional = [])
    {
        if (empty($parameter)) {
            return false;
        }

        $language = new ExpressionLanguage();
        if (!array_key_exists('user', $additional)) {
            $additional['user'] = $user;
        }

        return FeatureJudge::isTrue($language->evaluate($parameter, $additional));
    }
}
