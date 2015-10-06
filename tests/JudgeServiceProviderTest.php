<?php

namespace jlis\Tests\Judge;

use ArrayAccess;
use jlis\Judge\JudgeServiceProvider;
use Mockery;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class JudgeServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testRegister()
    {
        $app = Mockery::mock(ArrayAccess::class);
        $closure = Mockery::on(
            function ($closure) use ($app) {
                //static::assertInstanceOf(AbstractFeatureJudge::class, $closure());
                return true;
            }
        );

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('path.config')->andReturn('/some/config/path');
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn($config = Mockery::mock());

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $config->shouldReceive('get')->withAnyArgs()->times(4)->andReturn([]);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $config->shouldReceive('set')->withAnyArgs()->times(3)->andReturnUndefined();

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('make')->with('config')->once()->andReturn($config);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->with('feature', $closure)->once()->andReturnUndefined();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->with('value', $closure)->once()->andReturnUndefined();

        $provider = new JudgeServiceProvider($app);
        $provider->register();
    }

    public function testBoot()
    {
        $app = Mockery::mock(ArrayAccess::class);

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->times(3)->with('path.config')->andReturn('/some/config/path');

        $provider = new JudgeServiceProvider($app);
        $provider->boot();

        static::assertCount(3, $provider::pathsToPublish());
    }
}
