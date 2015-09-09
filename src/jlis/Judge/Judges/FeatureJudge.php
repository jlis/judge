<?php

namespace jlis\Judge\Judges;

use Illuminate\Support\Facades\Config;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureJudge extends AbstractFeatureJudge
{
    /**
     * Constructor
     *
     * @param array $voters
     */
    public function __construct(array $voters = [])
    {
        parent::__construct($voters);
    }

    /**
     * {@inheritDoc}
     */
    public function decide($feature, $user = null, $defaultIfNotFound = false)
    {
        if (!$this->featureExists($feature)) {
            return $defaultIfNotFound;
        }

        $rules = $this->getFeature($feature);
        if (!is_array($rules)) {
            return (bool) $rules;
        }

        $enabled = false;
        foreach ($rules as $rule) {
            if (true === $this->decideRules($rule, $user)) {
                $enabled = true;
            }
        }

        return $enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function featureExists($feature)
    {
        return (isset($this->features[$feature]) && false !== $this->features[$feature]);
    }

    /**
     * {@inheritDoc}
     */
    public function getFeature($feature)
    {
        return $this->getFeatures()[$feature];
    }

    /**
     * {@inheritDoc}
     */
    public function getFeatures()
    {
        return Config::get('features', []);
    }
}
