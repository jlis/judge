<?php

namespace Jlis\Tests\Judge\Adapters;

use Illuminate\Support\Facades\Redis;
use Jlis\Judge\Adapters\RedisAdapter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class RedisAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFeatures()
    {
        Redis::shouldReceive('hgetall')
            ->once()
            ->with('judge:features')
            ->andReturn([]);

        $adapter = new RedisAdapter();
        self::assertEquals([], $adapter->getFeatures());
    }

    public function testGetValues()
    {
        Redis::shouldReceive('hgetall')
            ->once()
            ->with('judge:values')
            ->andReturn([]);

        $adapter = new RedisAdapter();
        self::assertEquals([], $adapter->getValues());
    }
}
