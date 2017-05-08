<?php

namespace Jlis\Judge\Adapters;

use Illuminate\Support\Facades\Redis;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class RedisAdapter implements AdapterInterface
{
    const KEY_FEATURES = 'judge:features';
    const KEY_VALUES = 'judge:values';

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return Redis::hgetall(self::KEY_FEATURES);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return Redis::hgetall(self::KEY_VALUES);
    }
}
