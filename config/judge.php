<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The used storage adapter to load the features and values.
    |
    | The following adapters are available by default:
    | "config" => loads from the features.php and values.php config file
    | "redis" => loads from the "judge:features" and "judge:values" redis maps
    | "cache" => loads from the "judge:features" and "judge:values" cache
    |
    | If you want to use your own adapter, just implement the
    | Jlis\Judge\Adapters\AdapterInterface and set your class like
    | 'adapter' => My\Very\Own\Adapter::class,
    |--------------------------------------------------------------------------
    */
    'adapter' => 'config',

    /*
    |--------------------------------------------------------------------------
    | The voter configuration
    |
    | The key defines the name of the voter which is used in the features/values.
    |--------------------------------------------------------------------------
    */
    'voters' => [
        'debug' => Jlis\Judge\Voters\DebugVoter::class,
        'environment' => Jlis\Judge\Voters\EnvironmentVoter::class,
        'expression_language' => Jlis\Judge\Voters\ExpressionLanguageVoter::class,
        'feature' => Jlis\Judge\Voters\FeatureVoter::class,
        'value' => Jlis\Judge\Voters\ValueVoter::class,
        'random' => Jlis\Judge\Voters\RandomVoter::class,
    ],

];
