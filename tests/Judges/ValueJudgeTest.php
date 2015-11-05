<?php

namespace jlis\Tests\Judge\Judges;

use jlis\Judge\Adapters\AdapterInterface;
use jlis\Judge\Judges\ValueJudge;
use jlis\Tests\Judge\StdObject;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class ValueJudgeTest extends \PHPUnit_Framework_TestCase
{
    public function testValueNotFound()
    {
        $judge = new ValueJudge($this->getAdapter([]));
        static::assertFalse($judge->decide('missing_value'));
    }

    public function testValueNotFoundWithDefaultValue()
    {
        $judge = new ValueJudge($this->getAdapter([]));
        static::assertEquals('missing', $judge->decide('missing_value', null, 'missing'));
    }

    public function testValueIsString()
    {
        $adapter = $this->getAdapter(['existing_value' => 'value']);

        $judge = new ValueJudge($adapter);
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithNoRules()
    {
        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    'value',
                ],
            ]
        );

        $judge = new ValueJudge($adapter);
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testValueWithARuleAndEmptyFilters()
    {
        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    'value' => 'value',
                    'filters' => [],
                ],
            ]
        );

        $judge = new ValueJudge($adapter);
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testValueWithARuleAndANonExistingVoter()
    {
        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    'value' => true,
                    'filters' => ['missing_voter'],
                ],
            ]
        );

        $judge = new ValueJudge($adapter);
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testFeatureWithARuleAndANonExistingVoterClass()
    {
        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => true,
                        'filters' => ['missing_voter'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('missing_voter' => 'invalid_class'));
        static::assertFalse($judge->decide('existing_value'));
    }

    public function testFeatureWithARuleAndAInvalidVoter()
    {
        $foo = new StdObject();
        $foo->{'vote'} = function () {
            return true;
        };

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => get_class($foo)));
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
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($voterResult);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'value',
                        'filters' => ['existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
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

    /**
     * @dataProvider valueWithANegatedRuleAndAExistingVoterProvider
     *
     * @param bool  $expected
     * @param mixed $voterResult
     */
    public function testValueWithANegatedRuleAndAExistingVoter($expected, $voterResult)
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($voterResult);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'value',
                        'filters' => ['!existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
        static::assertEquals($expected, $judge->decide('existing_value'));
    }

    /**
     * @return array
     */
    public function valueWithANegatedRuleAndAExistingVoterProvider()
    {
        return array(
            array('value', false),
            array(false, true),
        );
    }

    public function testValueWithARuleAndAExistingVoterAndAParameter()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('parameter', null, [])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'value',
                        'filters' => ['existing_voter:parameter'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithARuleAndAExistingVoterAndMultipleParameters()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('firstParam', null, ['secondParam'])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'value',
                        'filters' => ['existing_voter:firstParam:secondParam'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value'));
    }

    public function testValueWithARuleAndAExistingVoterAndAUser()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('firstParam', 'Mike', ['secondParam'])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'value',
                        'filters' => ['existing_voter:firstParam:secondParam'],
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
        static::assertEquals('value', $judge->decide('existing_value', 'Mike'));
    }

    public function testValueWithARuleAndDefaultValue()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->andReturn(false);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'foo',
                        'filters' => ['existing_voter:parameter'],
                    ],
                    [
                        'value' => 'bar',
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('existing_voter' => $voterMock));
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
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'foo',
                        'filters' => ['voter_one', 'voter_two'],
                    ],
                    [
                        'value' => 'bar',
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
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
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        $adapter = $this->getAdapter(
            [
                'existing_value' => [
                    [
                        'value' => 'orange',
                        'filters' => ['voter_one'],
                    ],
                    [
                        'value' => 'green',
                        'filters' => ['voter_two'],
                    ],
                    [
                        'value' => 'blue',
                    ],
                ],
            ]
        );

        $judge = new ValueJudge($adapter, array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
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

    /**
     * @param array $returnValue
     *
     * @return AdapterInterface|\Mockery\MockInterface
     */
    private function getAdapter(array $returnValue = [])
    {
        $adapterMock = \Mockery::mock('jlis\Judge\Adapters\AdapterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $adapterMock->shouldReceive('getValues')->once()->andReturn($returnValue);

        return $adapterMock;
    }
}
