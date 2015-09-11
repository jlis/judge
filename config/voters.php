<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The voter configuration
    |--------------------------------------------------------------------------
    */
    'debug'               => \jlis\Judge\Voters\DebugVoter::class,
    'expression_language' => \jlis\Judge\Voters\ExpressionLanguageVoter::class,
    'feature'             => \jlis\Judge\Voters\FeatureVoter::class,
    'value'               => \jlis\Judge\Voters\ValueVoter::class,

];
