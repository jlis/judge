<?php

namespace jlis\Judge\Judges;

use jlis\Judge\Adapters\AdapterInterface;

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
     * @param AdapterInterface $adapter
     * @param array            $voters
     */
    public function __construct(AdapterInterface $adapter, array $voters = [])
    {
        parent::__construct($adapter, $voters);
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
