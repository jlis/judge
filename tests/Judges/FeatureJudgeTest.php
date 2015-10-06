<?php

namespace jlis\Tests\Judge\Judges;

use Illuminate\Support\Facades\Config;
use jlis\Judge\Judges\FeatureJudge;
use jlis\Tests\Judge\StdObject;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureJudgeTest extends \PHPUnit_Framework_TestCase
{
    public function testFeatureNotFound()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn([]);

        $judge = new FeatureJudge();
        static::assertFalse($judge->decide('missingFeature'));
    }

    public function testFeatureNotFoundWithDefaultValue()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn([]);

        $judge = new FeatureJudge();
        static::assertEquals('missing', $judge->decide('missingFeature', null, 'missing'));
    }

    public function testFeatureIsString()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => true,
                ]
            );

        $judge = new FeatureJudge();
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithNoRules()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        true,
                    ],
                ]
            );

        $judge = new FeatureJudge();
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
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value' => $value,
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge();
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleWithoutFiltersProvider()
    {
        return array(
            array(true, true),
            array(true, 'true'),
            array(true, '1'),
            array(true, 'on'),
            array(false, false),
            array(false, 'false'),
            array(false, '-1'),
            array(false, '0'),
            array(false, 'off'),
        );
    }

    public function testFeatureWithARuleAndEmptyFilters()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => [],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge();
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndANonExistingVoter()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['missing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge();
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndANonExistingVoterClass()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['missing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('missing_voter' => 'invalid_class'));
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAInvalidVoter()
    {
        $foo = new StdObject();
        $foo->{'vote'} = function () {
            return true;
        };

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['existing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => get_class($foo)));
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
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($value);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['existing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleAndAExistingVoterProvider()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider featureWithANegatedRuleAndAExistingVoterProvider
     *
     * @param bool  $expected
     * @param mixed $value
     */
    public function testFeatureWithANegatedRuleAndAExistingVoter($expected, $value)
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with(null, null, [])->andReturn($value);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['!existing_voter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithANegatedRuleAndAExistingVoterProvider()
    {
        return array(
            array(false, true),
            array(true, false),
        );
    }

    public function testFeatureWithARuleAndAExistingVoterAndAParameter()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('parameter', null, [])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['existing_voter:parameter'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAExistingVoterAndMultipleParameters()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('firstParam', null, ['secondParam'])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['existing_voter:firstParam:secondParam'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleAndAExistingVoterAndAUser()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->with('firstParam', 'Mike', ['secondParam'])->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['existing_voter:firstParam:secondParam'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
        static::assertTrue($judge->decide('existingFeature', 'Mike'));
    }

    public function testFeatureWithARuleAndDefaultValue()
    {
        $voterMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterMock->shouldReceive('vote')->once()->andReturn(false);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => false,
                            'filters' => ['existing_voter:parameter'],
                        ],
                        [
                            'value' => true,
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('existing_voter' => $voterMock));
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
        $voterOneMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['voter_one', 'voter_two'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithARuleAndMultipleFiltersProvider()
    {
        return array(
            array(true, true, true),
            array(false, false, false),
            array(false, true, false),
            array(false, false, true),
        );
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
        $voterOneMock = \Mockery::mock('jlis\Judge\Contracts\VoterInterface');
        $voterTwoMock = clone $voterOneMock;
        $voterOneMock->shouldReceive('vote')->once()->andReturn($voterOneResult);
        $voterTwoMock->shouldReceive('vote')->once()->andReturn($voterTwoResult);

        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn(
                [
                    'existingFeature' => [
                        [
                            'value'   => true,
                            'filters' => ['voter_one'],
                        ],
                        [
                            'value'   => true,
                            'filters' => ['voter_two'],
                        ],
                    ],
                ]
            );

        $judge = new FeatureJudge(array('voter_one' => $voterOneMock, 'voter_two' => $voterTwoMock));
        static::assertEquals($expected, $judge->decide('existingFeature'));
    }

    /**
     * @return array
     */
    public function featureWithMultipleRulesAndAFilterProvider()
    {
        return array(
            array(true, true, true),
            array(false, false, false),
            array(true, true, false),
            array(true, false, true),
        );
    }
}
