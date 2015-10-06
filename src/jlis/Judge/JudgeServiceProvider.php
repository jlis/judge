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
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $this->loadConfigs();
        $voters = $app->make('config')->get('voters');

        $app->bind(
            'feature',
            function() use ($voters) {
                return new FeatureJudge($voters);
            }
        );

        $app->bind(
            'value',
            function() use ($voters) {
                return new ValueJudge($voters);
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
        foreach ($this->getConfigs() as $key => $path) {
            $publishes[$path] = $this->app['path.config'].DIRECTORY_SEPARATOR.$key.'.php';
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
            'voters'   => __DIR__.'/../../../config/voters.php',
        );
    }
}
