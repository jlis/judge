<?php

namespace jlis\Tests\Judge\Adapters;

use Illuminate\Support\Facades\Cache;
use jlis\Judge\Adapters\CacheAdapter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class CacheAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFeatures()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('judge:features', [])
            ->andReturn([]);

        $adapter = new CacheAdapter();
        self::assertEquals([], $adapter->getFeatures());
    }

    public function testGetValues()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('judge:values', [])
            ->andReturn([]);

        $adapter = new CacheAdapter();
        self::assertEquals([], $adapter->getValues());
    }
}
