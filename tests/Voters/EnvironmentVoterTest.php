<?php

namespace Jlis\Tests\Judge\Voters;

use Illuminate\Support\Facades\App;
use Jlis\Judge\Voters\EnvironmentVoter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class EnvironmentVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool $expected
     * @param bool $actual
     * @param mixed $parameter
     */
    public function test($expected, $actual, $parameter)
    {
        App::shouldReceive('environment')
            ->once()
            ->with($parameter)
            ->andReturn($actual);

        $voter = new EnvironmentVoter();
        static::assertEquals($expected, $voter->vote($parameter));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [true, true, 'local'],
            [false, false, 'local'],
        ];
    }
}

