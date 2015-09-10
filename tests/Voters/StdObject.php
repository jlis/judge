<?php

namespace jlis\Tests\Judge\Voters;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 * @see    http://stackoverflow.com/a/2938020
 */
class StdObject
{
    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;

            return call_user_func_array($func, $args);
        }
    }
}