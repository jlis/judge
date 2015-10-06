<?php

namespace jlis\Tests\Judge\Voters;

use Illuminate\Support\Facades\Config;
use jlis\Judge\Voters\DebugVoter;

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
        return array(
            array(true, true, 'true'),
            array(false, false, 'false'),
            array(false, null, 'true'),
            array(false, null, ''),
        );
    }
}
