<?php

namespace Jlis\Judge\Adapters;

use Illuminate\Support\Facades\Cache;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class CacheAdapter implements AdapterInterface
{
    const KEY_FEATURES = 'judge:features';
    const KEY_VALUES = 'judge:values';

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return Cache::get(self::KEY_FEATURES, []);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return Cache::get(self::KEY_VALUES, []);
    }
}
