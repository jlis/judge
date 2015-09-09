<?php

namespace jlis\Judge\Judges;

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
     * @param array $voters
     */
    public function __construct(array $voters = [])
    {
        parent::__construct($voters);
        $this->values = $this->getValues();
    }

    /**
     * {@inheritDoc}
     */
    abstract public function decide($value, $user = null, $defaultValue = false);

    /**
     * {@inheritDoc}
     */
    abstract public function valueExists($value);

    /**
     * {@inheritDoc}
     */
    abstract public function getValues();
}
