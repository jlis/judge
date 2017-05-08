<?php

namespace Jlis\Judge\Adapters;

use Illuminate\Support\Facades\Config;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ConfigAdapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return Config::get('features', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return Config::get('values', []);
    }
}
