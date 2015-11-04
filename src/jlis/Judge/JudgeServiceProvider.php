<?php

namespace jlis\Judge;

use Illuminate\Support\ServiceProvider;
use jlis\Judge\Judges\FeatureJudge;
use jlis\Judge\Judges\ValueJudge;

/**
 * @author Julius Ehrlich <julius@ehrlich-bros.de>
 */
class JudgeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $app = $this->app;
        $this->loadConfigs();
        $config = $app->make('config')->get('judge');
        $adapter = $this->app->make($this->getAdapterClass($config));
        $voters = isset($config['voters']) ? $config['voters'] : [];

        $app->bind(
            'feature',
            function () use ($adapter, $voters) {
                return new FeatureJudge($adapter, $voters);
            }
        );

        $app->bind(
            'value',
            function () use ($adapter, $voters) {
                return new ValueJudge($adapter, $voters);
            }
        );
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $publishes = [];
        $app = $this->app;
        foreach ($this->getConfigs() as $key => $path) {
            /* @var $app array */
            $publishes[$path] = $app['path.config'].DIRECTORY_SEPARATOR.$key.'.php';
        }
        $this->publishes($publishes, 'config');
    }

    private function loadConfigs()
    {
        foreach ($this->getConfigs() as $key => $path) {
            $this->mergeConfigFrom($path, $key);
        }
    }

    /**
     * @return array
     */
    private function getConfigs()
    {
        return array(
            'features' => __DIR__.'/../../../config/features.php',
            'values' => __DIR__.'/../../../config/values.php',
            'judge' => __DIR__.'/../../../config/judge.php',
        );
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function getAdapterClass(array $config)
    {
        $map = [
            'config' => Adapters\ConfigAdapter::class,
            'redis' => Adapters\RedisAdapter::class,
            'cache' => Adapters\CacheAdapter::class,
        ];

        $className = isset($config['adapter']) ? $config['adapter'] : 'config';
        if (isset($map[$className])) {
            return $map[$className];
        }

        return $className;
    }
}
