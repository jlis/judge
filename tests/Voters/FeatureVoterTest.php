<?php

namespace jlis\Tests\Judge\Voters;

use jlis\Judge\Feature;
use jlis\Tests\Judge\StdObject;
use jlis\Judge\Voters\FeatureVoter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class FeatureVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     *
     * @param bool  $expected
     * @param mixed $featureName
     * @param mixed $featureResult
     * @param mixed $user
     * @param array $additional
     * @param bool  $expectedDefaultValue
     */
    public function test(
        $expected,
        $featureName,
        $featureResult,
        $user = null,
        array $additional = [],
        $expectedDefaultValue = false
    ) {
        Feature::shouldReceive('decide')
            ->once()
            ->with($featureName, $user, $expectedDefaultValue)
            ->andReturn($featureResult);

        $voter = new FeatureVoter();
        static::assertEquals($expected, $voter->vote($featureName, $user, $additional));
    }

    /**
     * @return array
     */
    public function provider()
    {
        return [
            [true, 'some_feature', true],
            [false, 'some_feature', false],
            [true, 'some_feature', true, new StdObject()],
            [true, 'some_feature', true, null, ['test'], false],
            [true, 'some_feature', true, null, ['defaultIfNotFound' => true], true],
        ];
    }
}
