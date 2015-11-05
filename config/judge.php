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
    | jlis\Judge\Adapters\AdapterInterface and set your class like
    | 'adapter' => my\very\own\adapter::class,
    |--------------------------------------------------------------------------
    */
    'adapter' => 'config',

    /*
    |--------------------------------------------------------------------------
    | The voter configuration
    |
    | The key defines the name of the voter which is used in the flaggings.
    |--------------------------------------------------------------------------
    */
    'voters' => [
        'debug' => \jlis\Judge\Voters\DebugVoter::class,
        'expression_language' => \jlis\Judge\Voters\ExpressionLanguageVoter::class,
        'feature' => \jlis\Judge\Voters\FeatureVoter::class,
        'value' => \jlis\Judge\Voters\ValueVoter::class,
        'random' => \jlis\Judge\Voters\RandomVoter::class,
    ],

];
