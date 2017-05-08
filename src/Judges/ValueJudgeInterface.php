<?php

namespace Jlis\Judge\Judges;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
interface ValueJudgeInterface
{
    /**
     * Decides which value should be returned, based on the given filters.
     *
     * @param string $value
     * @param mixed  $user
     * @param bool   $defaultValue
     *
     * @return mixed
     */
    public function decide($value, $user = null, $defaultValue = false);

    /**
     * Checks if a value exists.
     *
     * @param string $value
     *
     * @return bool
     */
    public function valueExists($value);

    /**
     * Returns all values.
     *
     * @return array
     */
    public function getValues();
}
