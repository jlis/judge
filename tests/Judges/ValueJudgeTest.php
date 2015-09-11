<?php

namespace jlis\Tests\Judge\Judges;

use Illuminate\Support\Facades\Config;
use jlis\Judge\Judges\ValueJudge;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ValueJudgeTest extends \PHPUnit_Framework_TestCase
{
    public function testValueNotFound()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn([]);

        $judge = new ValueJudge();
        static::assertFalse($judge->decide('missing_value'));
    }

    public function testValueNotFoundWithDefaultValue()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn([]);

        $judge = new ValueJudge();
        static::assertEquals('missing', $judge->decide('missing_value', null, 'missing'));
    }

    public function testValueIsString()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => 'value',
                ]
            );

        $judge = new ValueJudge();
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithNoRules()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        'value'
                    ],
                ]
            );

        $judge = new ValueJudge();
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testValueWithARuleAndEmptyFilters()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        'value'   => 'value',
                        'filters' => [],
                    ]
                ]
            );

        $judge = new ValueJudge();
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testValueWithARuleAndANonExistingVoter()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        'value'   => true,
                        'filters' => ['missing_voter'],
                    ]
                ]
            );

        $judge = new ValueJudge();
        static::assertFalse($judge->decide('existing_value'));
    }

    /**
     * @dataProvider valueWithARuleAndAExistingVoterProvider
     *
     * @param bool  $expected
     * @param mixed $voterResult
     */
    public function testValueWithARuleAndAExistingVoter($expected, $voterResult)
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($voterResult);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'value',
                            'filters' => ['existing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('existing_voter' => $voterMock));
        static::assertEquals($expected, $judge->decide('existing_value'));
    }

    /**
     * @return array
     */
    public function valueWithARuleAndAExistingVoterProvider()
    {
        return array(
            array('value', true),
            array(false, false),
        );
    }

    public function testValueWithARuleAndAExistingVoterAndAParameter()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('parameter', null, [])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'value',
                            'filters' => ['existing_voter:parameter'],
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithARuleAndAExistingVoterAndMultipleParameters()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('firstParam', null, ['secondParam'])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'value',
                            'filters' => ['existing_voter:firstParam:secondParam'],
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithARuleAndAExistingVoterAndAUser()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('firstParam', 'Mike', ['secondParam'])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'value',
                            'filters' => ['existing_voter:firstParam:secondParam'],
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value', 'Mike'));
    }

    public function testValueWithARuleAndDefaultValue()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->andReturn(false);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'foo',
                            'filters' => ['existing_voter:parameter'],
                        ],
                        [
                            'value' => 'bar',
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('existing_voter' => $voterMock));
        static::assertEquals('bar', $judge->decide('existing_value'));
    }

    /**
     * @dataProvider valueWithARuleAndMultipleFiltersProvider
     *
     * @param $expected
     * @param $voterOneResult
     * @param $voterTwoResult
     */
    public function testValueWithARuleAndMultipleFilters($expected, $voterOneResult, $voterTwoResult)
    {
        $voterOneMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'foo',
                            'filters' => ['voter_one', 'voter_two'],
                        ],
                        [
                            'value' => 'bar',
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
        static::assertEquals($expected, $judge->decide('existing_value'));
    }

    /**
     * @return array
     */
    public function valueWithARuleAndMultipleFiltersProvider()
    {
        return array(
            array('foo', true, true),
            array('bar', false, false),
            array('bar', true, false),
            array('bar', false, true),
        );
    }

    /**
     * @dataProvider valueWithMultipleRulesAndAFilterProvider
     *
     * @param $expected
     * @param $voterOneResult
     * @param $voterTwoResult
     */
    public function testValueWithMultipleRulesAndAFilter($expected, $voterOneResult, $voterTwoResult)
    {
        $voterOneMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        Config::shouldReceive('get')
            ->once()
            ->with('values', [])
            ->andReturn(
                [
                    'existing_value' => [
                        [
                            'value'   => 'orange',
                            'filters' => ['voter_one'],
                        ],
                        [
                            'value'   => 'green',
                            'filters' => ['voter_two'],
                        ],
                        [
                            'value' => 'blue',
                        ],
                    ],
                ]
            );

        $judge = new ValueJudge(array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
        static::assertEquals($expected, $judge->decide('existing_value'));
    }

    /**
     * @return array
     */
    public function valueWithMultipleRulesAndAFilterProvider()
    {
        return array(
            array('orange', true, true),
            array('blue', false, false),
            array('orange', true, false),
            array('green', false, true),
        );
    }
}