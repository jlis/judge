<?php

namespace jlis\Judge\Judges;

use jlis\Judge\Adapters\AdapterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
abstract class AbstractValueJudge extends Judge implements ValueJudgeInterface
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     * @param array            $voters
     */
    public function __construct(AdapterInterface $adapter, array $voters = [])
    {
        parent::__construct($adapter, $voters);
        $this->values = $this->getValues();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function decide($value, $user = null, $defaultValue = false);

    /**
     * {@inheritdoc}
     */
    abstract public function valueExists($value);

    /**
     * {@inheritdoc}
     */
    abstract public function getValues();
}
