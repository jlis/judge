<?php

namespace Jlis\Tests\Judge\Adapters;

use Illuminate\Support\Facades\Config;
use Jlis\Judge\Adapters\ConfigAdapter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ConfigAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFeatures()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn([]);

        $adapter = new ConfigAdapter();
        self::assertEquals([], $adapter->getFeatures());
    }

    public function testGetValues()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn([]);

        $adapter = new ConfigAdapter();
        self::assertEquals([], $adapter->getValues());
    }
}
