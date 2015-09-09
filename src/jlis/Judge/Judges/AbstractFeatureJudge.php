<?php

namespace jlis\Judge\Judges;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
abstract class AbstractFeatureJudge extends Judge implements FeatureJudgeInterface
{
    /**
     * @var array
     */
    protected $features = [];

    /**
     * Constructor.
     *
     * @param array $voters
     */
    public function __construct(array $voters = [])
    {
        parent::__construct($voters);
        $this->features = $this->getFeatures();
    }

    /**
     * {@inheritDoc}
     */
    abstract public function decide($feature, $user = null, $defaultIfNotFound = false);

    /**
     * {@inheritDoc}
     */
    abstract public function featureExists($feature);

    /**
     * {@inheritDoc}
     */
    abstract public function getFeature($feature);

    /**
     * {@inheritDoc}
     */
    abstract public function getFeatures();
}
