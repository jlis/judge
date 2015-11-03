<?php

namespace jlis\Judge;

use Illuminate\Support\ServiceProvider;
use jlis\Judge\Adapters\ConfigAdapter;
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
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $this->loadConfigs();
        $config = $app->make('config')->get('judge');
        $adapterName = isset($config['adapter']) ? $config['adapter'] : ConfigAdapter::class;
        $adapter = $this->app->make($adapterName);
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
     *
     * @return void
     */
    public function boot()
    {
        $publishes = [];
        $app = $this->app;
        foreach ($this->getConfigs() as $key => $path) {
            /** @var $app array */
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
            'values'   => __DIR__.'/../../../config/values.php',
            'judge'   => __DIR__.'/../../../config/judge.php',
        );
    }
}
