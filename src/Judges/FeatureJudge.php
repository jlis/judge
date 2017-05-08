<?php

namespace Jlis\Judge\Judges;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureJudge extends AbstractFeatureJudge
{
    /**
     * {@inheritdoc}
     */
    public function decide($feature, $user = null, $defaultIfNotFound = false)
    {
        if (! $this->featureExists($feature)) {
            return $defaultIfNotFound;
        }

        $rules = $this->getFeature($feature);
        if (! is_array($rules)) {
            return (bool) $rules;
        }

        $enabled = false;
        foreach ($rules as $rule) {
            if (true === self::isTrue($this->decideRules($rule, $user))) {
                $enabled = true;
            }
        }

        return $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function featureExists($feature)
    {
        return isset($this->features[$feature]) && false !== $this->features[$feature];
    }

    /**
     * {@inheritdoc}
     */
    public function getFeature($feature)
    {
        return $this->features[$feature];
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return $this->adapter->getFeatures();
    }

    /**
     * @see http://php.net/manual/de/function.boolval.php#116547
     *
     * @param mixed      $val
     * @param bool|false $returnNull
     *
     * @return bool|mixed|null
     */
    public static function isTrue($val, $returnNull = false)
    {
        $boolVal = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val);

        return $boolVal === null && ! $returnNull ? false : $boolVal;
    }
}
