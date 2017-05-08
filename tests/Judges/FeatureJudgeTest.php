<?php

namespace Jlis\Tests\Judge\Judges;

use Jlis\Tests\Judge\StdObject;
use Jlis\Judge\Judges\FeatureJudge;
use Jlis\Judge\Adapters\AdapterInterface;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureJudgeTest extends \PHPUnit_Framework_TestCase
{
    public function testFeatureNotFound()
    {
        $judge = new FeatureJudge($this->getAdapter([]));
        static::assertFalse($judge->decide('missingFeature'));
    }

    public function testFeatureNotFoundWithDefaultValue()
    {
        $judge = new FeatureJudge($this->getAdapter([]));
        static::assertEquals('missing', $judge->decide('missingFeature', null, 'missing'));
    }

    public function testFeatureIsString()
    {
        $adapter = $this->getAdapter(['existingFeature' => true]);

        $judge = new FeatureJudge($adapter);
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithNoRules()
    {
        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    true,
                ],
            ]
        );

        $judge = new FeatureJudge($adapter);
        static::assertFalse($judge->decide('existingFeature'));
    }

    /**
     * @dataProvider featureWithARuleWithoutFiltersProvider
     *
     * @param bool  $expected
     * @param mixed $value
     */
    public function testFeatureWithARuleWithoutFilters($expected, $value)
    {
        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => $value,
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter);
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleWithoutFiltersProvider()
    {
        return [
            [true, true],
            [true, 'true'],
            [true, '1'],
            [true, 'on'],
            [false, false],
            [false, 'false'],
            [false, '-1'],
            [false, '0'],
            [false, 'off'],
        ];
    }

    public function testFeatureWithARuleAndEmptyFilters()
    {
        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => [],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter);
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndANonExistingVoter()
    {
        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['missing_voter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter);
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndANonExistingVoterClass()
    {
        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['missing_voter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['missing_voter' => 'invalid_class']);
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAInvalidVoter()
    {
        $foo = new StdObject();
        $foo->{'vote'} = function () {
            return true;
        };

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => get_class($foo)]);
        static::assertFalse($judge->decide('existingFeature'));
    }

    /**
     * @dataProvider featureWithARuleAndAExistingVoterProvider
     *
     * @param bool  $expected
     * @param mixed $value
     */
    public function testFeatureWithARuleAndAExistingVoter($expected, $value)
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($value);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleAndAExistingVoterProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    /**
     * @dataProvider featureWithANegatedRuleAndAExistingVoterProvider
     *
     * @param bool  $expected
     * @param mixed $value
     */
    public function testFeatureWithANegatedRuleAndAExistingVoter($expected, $value)
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($value);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['!existing_voter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithANegatedRuleAndAExistingVoterProvider()
    {
        return [
            [false, true],
            [true, false],
        ];
    }

    public function testFeatureWithARuleAndAExistingVoterAndAParameter()
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('parameter', null, [])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter:parameter'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAExistingVoterAndMultipleParameters()
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('firstParam', null, ['secondParam'])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter:firstParam:secondParam'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAExistingVoterAndAUser()
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->with('firstParam', 'Mike', ['secondParam'])->andReturn(true);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['existing_voter:firstParam:secondParam'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertTrue($judge->decide('existingFeature', 'Mike'));
    }

    public function testFeatureWithARuleAndDefaultValue()
    {
        $voterMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterMock->shouldReceive('vote')->once()->andReturn(false);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => false,
                        'filters' => ['existing_voter:parameter'],
                    ],
                    [
                        'value' => true,
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['existing_voter' => $voterMock]);
        static::assertTrue($judge->decide('existingFeature'));
    }

    /**
     * @dataProvider featureWithARuleAndMultipleFiltersProvider
     *
     * @param $expected
     * @param $voterOneResult
     * @param $voterTwoResult
     */
    public function testFeatureWithARuleAndMultipleFilters($expected, $voterOneResult, $voterTwoResult)
    {
        $voterOneMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['voter_one', 'voter_two'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock]);
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleAndMultipleFiltersProvider()
    {
        return [
            [true, true, true],
            [false, false, false],
            [false, true, false],
            [false, false, true],
        ];
    }

    /**
     * @dataProvider featureWithMultipleRulesAndAFilterProvider
     *
     * @param $expected
     * @param $voterOneResult
     * @param $voterTwoResult
     */
    public function testFeatureWithMultipleRulesAndAFilter($expected, $voterOneResult, $voterTwoResult)
    {
        $voterOneMock = \Mockery::mock('Jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        $adapter = $this->getAdapter(
            [
                'existingFeature' => [
                    [
                        'value' => true,
                        'filters' => ['voter_one'],
                    ],
                    [
                        'value' => true,
                        'filters' => ['voter_two'],
                    ],
                ],
            ]
        );

        $judge = new FeatureJudge($adapter, ['voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock]);
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithMultipleRulesAndAFilterProvider()
    {
        return [
            [true, true, true],
            [false, false, false],
            [true, true, false],
            [true, false, true],
        ];
    }

    /**
     * @param array $returnValue
     *
     * @return AdapterInterface|\Mockery\MockInterface
     */
    private function getAdapter(array $returnValue = [])
    {
        $adapterMock = \Mockery::mock('Jlis\Judge\Adapters\AdapterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $adapterMock->shouldReceive('getFeatures')->once()->andReturn($returnValue);

        return $adapterMock;
    }
}
