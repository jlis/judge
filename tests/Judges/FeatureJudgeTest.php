<?php

namespace jlis\Tests\Judge\Judges;

use Illuminate\Support\Facades\Config;
use jlis\Judge\Judges\FeatureJudge;

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
            ->andReturn([
                'existingFeature' => true
            ]);

        $judge = new FeatureJudge();
        static::assertTrue($judge->decide('existingFeature'));
    }

    public function testFeatureWithNoRules()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn([
                'existingFeature' => [
                    true
                ]
            ]);

        $judge = new FeatureJudge();
        static::assertFalse($judge->decide('existingFeature'));
    }

    public function testFeatureWithARuleWithoutFilters()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('features', [])
            ->andReturn([
                'existingFeature' => [
                    []
                ]
            ]);

        $judge = new FeatureJudge();
        static::assertFalse($judge->decide('existingFeature'));
    }
}
