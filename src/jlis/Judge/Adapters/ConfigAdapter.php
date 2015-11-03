<?php

namespace jlis\Judge\Adapters;

use Illuminate\Support\Facades\Config;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ConfigAdapter implements AdapterInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFeatures()
    {
        return Config::get('features', []);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return Config::get('values', []);
    }
}
