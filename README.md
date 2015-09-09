# Judge - A feature and value decider for Laravel 5

## Installation

First, pull in the package through Composer.

```js
"require": {
    "jlis/judge": "dev-master"
}
```

And then include the service provider within `app/config/app.php`.

```php
'providers' => [
    'jlis\Judge\JudgeServiceProvider.php'
];
```

And, for convenience, add a facade alias to this same file at the bottom:

```php
'aliases' => [
    'Feature' => 'jlis\Judge\Feature',
    'Value' => 'jlis\Judge\Value',
];
```

Copy the package configs to your local config with the publish command:

```
php artisan vendor:publish
```

## Features

The feature configuration is stored in:

```
app/config/features.php
```

A **feature** is defined as something which is either on or off and should be only used for that kind if toggles. Lets see:

```php
'show_memory_usage' => [
    [
        'value'   => true,
        'filters' => ['debug:true'],
    ],
],
```

This name of the feature is "show_memory_usage" and it should return true if the "debug" Voter return true (he checks if the debug mode is enabled or not, see *DebugVoter.php*)


## Values

The value configuration is stored in:

```
app/config/values.php
```

The **value** however is something which always returns a value (whoa) like a string or number for example:

```php
'hello_message' => [
    [
        'value'   => 'Hello %s',
        'filters' => ['expression_language:user !== null'],
    ],
    [
        'value' => 'Hello guest.',
    ],
]
```

This name of the value is "hello_message". It should return "Hello %s" if the expression voter return true (assuming the given user is not NULL). Otherwise it should return "Hello guest."

(*ExpressionVoter.php* check with the help of the Symfony Expression Language if the given expression is true)

## Voters

The actual voters can be registered here:

```
app/config/voters.php
```

The voters contain the logic to decide, if the given filter should return true or false. This decides either a feature is on or off or what a value should return regarding to his config.

## Usage

Within your controllers, you can use this for example...

```php
$helloMessage = Value::decide('hello_message', $this->getUser());
echo sprintf($helloMessage, $this->getUser()->name);
```

Or this for example:

```php
if (Feature::decide('show_memory_usage', Auth::user())) {
    echo 'Memory usage: ' . memory_get_usage();
}
```

