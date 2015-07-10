<?php

namespace Judge;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class Judge
{
    /**
     * @var array
     */
    private $features;
    /**
     * @var array
     */
    private $values;

    /**
     * Constructor.
     *
     * @param array $features
     * @param array $values
     */
    public function __construct(array $features = [], array $values = [])
    {
        $this->features = $features;
        $this->values = $values;
    }

    /**
     * Decides if a feature is active or not
     *
     * @param string $feature
     * @param bool   $activeIfNotFound
     * @param array  $parameters
     */
    public function decideFeature($feature, $activeIfNotFound = false, array $parameters = [])
    {

    }

    /**
     * Decides if a what value to return
     *
     * @param string $value
     * @param null   $default
     * @param array  $parameters
     */
    public function decideValue($value, $default = null, array $parameters = [])
    {

    }

    /**
     * Returns the feature config
     *
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Replaces the feature config with a new one
     *
     * @param array $features
     */
    public function setFeatures(array $features)
    {
        $this->features = $features;
    }

    /**
     * Adds a new feature to the feature config
     *
     * @param string $feature
     * @param bool   $enabled
     * @param array  $filters
     */
    public function addFeature($feature, $enabled = false, array $filters = [])
    {

    }

    /**
     * Removes a feature from the feature config
     *
     * @param string $feature
     */
    public function removeFeature($feature)
    {

    }

    /**
     * Returns the value config
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Replaces the value config with a new one
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }
}
