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
        $this->reloadFeatures();
    }

    /**
     * Reloads the features from the adapter
     */
    public function reloadFeatures() {
        $this->features = $this->getFeatures();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function decide($feature, $user = null, $defaultIfNotFound = false);

    /**
     * {@inheritdoc}
     */
    abstract public function featureExists($feature);

    /**
     * {@inheritdoc}
     */
    abstract public function getFeature($feature);

    /**
     * {@inheritdoc}
     */
    abstract public function getFeatures();
}
