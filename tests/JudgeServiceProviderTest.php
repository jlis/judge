<?php

namespace Jlis\Tests\Judge;

use Mockery;
use ArrayAccess;
use Jlis\Judge\JudgeServiceProvider;
use Jlis\Judge\Adapters\ConfigAdapter;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class JudgeServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @dataProvider registerProvider
     */
    public function testRegister(array $judgeConfig)
    {
        $app = Mockery::mock(ArrayAccess::class);
        $closure = Mockery::on(
            function ($closure) use ($app) {
                $result = $closure($this->getAdapterMock(), []);
                self::assertInstanceOf('Jlis\Judge\Judges\Judge', $result);

                return true;
            }
        );

        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('path.config')->andReturn('/some/config/path');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn($config = Mockery::mock());

        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $config->shouldReceive('get')->withArgs(['judge'])->times(1)->andReturn($judgeConfig);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $config->shouldReceive('get')->withAnyArgs()->times(3)->andReturn([]);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $config->shouldReceive('set')->withAnyArgs()->times(3)->andReturnUndefined();

        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('make')->with('config')->once()->andReturn($config);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('make')->with('Jlis\Judge\Adapters\ConfigAdapter')->once()->andReturn(
            $this->getAdapterMock()
        );
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->with('feature', $closure)->once()->andReturnUndefined();
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('bind')->with('value', $closure)->once()->andReturnUndefined();

        $provider = new JudgeServiceProvider($app);
        $provider->register();
    }

    /**
     * @return array
     */
    public function registerProvider()
    {
        return [
            [[]],
            [['adapter' => ConfigAdapter::class]],
        ];
    }

    public function testBoot()
    {
        $app = Mockery::mock(ArrayAccess::class);

        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $app->shouldReceive('offsetGet')->times(3)->with('path.config')->andReturn('/some/config/path');

        $provider = new JudgeServiceProvider($app);
        $provider->boot();

        static::assertCount(3, $provider::pathsToPublish());
    }

    /**
     * @return Mockery\MockInterface
     */
    private function getAdapterMock()
    {
        $mock = Mockery::mock('Jlis\Judge\Adapters\AdapterInterface');
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $mock->shouldReceive('getFeatures')->zeroOrMoreTimes()->andReturn([]);
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $mock->shouldReceive('getValues')->zeroOrMoreTimes()->andReturn([]);

        return $mock;
    }
}
