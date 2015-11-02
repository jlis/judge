<?php

namespace jlis\Tests\Judge\Voters;

use jlis\Judge\Voters\RandomVoter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class RandomVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool  $expected
     * @param mixed $parameter
     */
    public function test($expected, $parameter)
    {
        mt_srand(0); // seed the "Mersenne Twister algorithm" so it mt_rand(0, 100) returns always 45
        $voter = new RandomVoter();
        static::assertEquals($expected, $voter->vote($parameter));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [false, null],
            [false, 44],
            [false, 10],
            [true, 45],
            [true, 100],
        ];
    }
}
