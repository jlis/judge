<?php

namespace jlis\Judge;

use Illuminate\Support\Facades\Facade;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class Value extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'value';
    }
}
