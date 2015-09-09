<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hello message
    |--------------------------------------------------------------------------
    */
    'hello_message' => [
        [
            'value'   => 'Hello %s',
            'filters' => ['expression_language:user !== null'],
        ],
        [
            'value' => 'Hello guest.',
        ],
    ],

];
