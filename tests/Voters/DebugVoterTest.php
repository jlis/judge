<?php

namespace Jlis\Tests\Judge\Voters;

use Jlis\Judge\Voters\DebugVoter;
use Illuminate\Support\Facades\Config;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class DebugVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool  $expected
     * @param mixed $actual
     * @param mixed $parameter
     */
    public function test($expected, $actual, $parameter)
    {
        Config::shouldReceive('get')
            ->once()
            ->with('app.debug')
            ->andReturn($actual);

        $voter = new DebugVoter();
        static::assertEquals($expected, $voter->vote($parameter));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [true, true, 'true'],
            [false, false, 'false'],
            [false, null, 'true'],
            [false, null, ''],
        ];
    }
}
