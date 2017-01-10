<?php

namespace jlis\Tests\Judge\Voters;

use jlis\Judge\Value;
use jlis\Tests\Judge\StdObject;
use jlis\Judge\Voters\ValueVoter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ValueVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool  $expected
     * @param mixed $valueName
     * @param mixed $valueResult
     * @param mixed $user
     * @param array $additional
     * @param bool  $defaultValue
     */
    public function test(
        $expected,
        $valueName,
        $valueResult,
        $user = null,
        array $additional = [],
        $defaultValue = false
    ) {
        Value::shouldReceive('decide')
            ->once()
            ->with($valueName, $user, $defaultValue)
            ->andReturn($valueResult);

        $voter = new ValueVoter();
        static::assertEquals($expected, $voter->vote($valueName, $user, $additional));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            ['meow', 'some_value', 'meow'],
            [false, 'some_value', false],
            [true, 'some_value', true, new StdObject()],
            [true, 'some_value', true, null, ['test'], false],
            [true, 'some_value', true, null, ['defaultValue' => true], true],
        ];
    }
}
