<?php

namespace Jlis\Judge\Judges;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
interface FeatureJudgeInterface
{
    /**
     * Checks if the given feature is enabled (for the current user).
     *
     * @param string $feature
     * @param mixed  $user
     * @param bool   $defaultIfNotFound
     *
     * @return bool
     */
    public function decide($feature, $user = null, $defaultIfNotFound = false);

    /**
     * Checks if a feature exists.
     *
     * @param string $feature
     *
     * @return bool
     */
    public function featureExists($feature);

    /**
     * Returns a single feature.
     *
     * @param string $feature
     *
     * @return mixed
     */
    public function getFeature($feature);

    /**
     * Returns all features.
     *
     * @return array
     */
    public function getFeatures();
}
