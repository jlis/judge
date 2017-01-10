# Judge - A feature and value decider for Laravel 5 

The easy way to toggle/decide features and values.

[![Build Status](https://travis-ci.org/jlis/judge.svg?branch=master)](https://travis-ci.org/jlis/judge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jlis/judge/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jlis/judge/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jlis/judge/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jlis/judge/?branch=master)
[![StyleCI](https://styleci.io/repos/33918409/shield)](https://styleci.io/repos/33918409)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/db926e8a-da62-41ef-b689-77e9f48cf765/mini.png)](https://insight.sensiolabs.com/projects/db926e8a-da62-41ef-b689-77e9f48cf765)
[![Total Downloads](https://poser.pugx.org/jlis/judge/downloads)](https://packagist.org/packages/jlis/judge)

- [Installation](#installation).
- [Feature configuration](#features).
- [Feature configuration examples](#featuresExamples).
- [Value configuration](#values).
- [Value configuration examples](#valuesExamples).
- [Voters](#voters).
- [Adapters](#adapters).
- [Usage](#usage).
- [TBD](#tbd).


<a id="installation"></a>
## Installation

First, pull in the package through Composer.

```
composer require jlis/judge
```

And then include the service provider within `app/config/app.php`.

```php
'providers' => [
    \jlis\Judge\JudgeServiceProvider::class,
];
```

And, for convenience, add a facade alias to this same file at the bottom:

```php
'aliases' => [
    'Feature' => \jlis\Judge\Feature::class,
    'Value'   => \jlis\Judge\Value::class,
];
```

Copy the package configs to your local config with the publish command:

```
php artisan vendor:publish
```

<a id="features"></a>
## Feature configuration

The feature configuration is stored in:

```
app/config/features.php
```

A **feature** is defined as something which is either on or off and should be only used for that kind of toggles. Lets see:

```php
'show_memory_usage' => [
    [
        'value'   => true,
        'filters' => ['debug:true'],
    ],
],
```

The name of the feature is `show_memory_usage` and it should return true if the "debug" Voter returns true (it checks whether the debug mode is enabled or not, see *DebugVoter.php*)

Note that the default value of a **feature**, if not defined otherwise, is always **false**.

<a id="featuresExamples"></a>
## Feature configuration examples

A simple feature without any filters ( *note that the value can also be a string like "true", "on" or "1", it will be converted into a boolean* ):

```php
'enable_captcha' => [
    [
        'value'   => true,
    ],
],
```

A feature with multiple filters chained in an **AND** condition:

```php
'enable_captcha' => [
    [
        'value'   => true,
        'filters' => ['env:production', 'expression_language:user==null'],
    ],
],
```

A feature with multiple filters chained in an **OR** condition:

```php
'enable_captcha' => [
    [
        'value'   => true,
        'filters' => ['expression_language:user==null'],
    ],
    [
        'value'   => true,
        'filters' => ['env:production'],
    ],
],
```

A feature with a **negated** filter:

```php
'enable_debug_output' => [
    [
        'value'   => true,
        'filters' => ['!env:production'],
    ],
],
```

<a id="values"></a>
## Value configuration

The value configuration is stored in:

```
app/config/values.php
```

The **value** however is something which always returns a value (whoa) like a string or number for example:

```php
'greeting' => [
    [
        'value'   => 'Hello my lady!',
        'filters' => ['expression_language:user.getGender()=="female"'],
    ],
    [
        'value' => 'Hello sir.',
    ],
]
```

This name of the value is `greeting`. It should return "Hello my lady!" if the expression voter return true (assuming the given user is not NULL and it's gender is female). Otherwise it should return "Hello sir.".
(Sorry for the gender guessing)

(*ExpressionVoter.php* uses the Symfony Expression Language to check if the given expression is true)

<a id="valuesExamples"></a>
## Value configuration examples

A simple value without any filters :

```php
'package_price' => [
    [
        'value'   => 10.00,
    ],
],
```

A value with one filter and a default value:

```php
'package_price' => [
    [
        'value'   => 00.00,
        'filters' => ['expression_language:user.hasPlan("premium")'],
    ],
    [
        'value'   => 10.00,
    ],
],
```

A value with multiple filters chained in an **AND** condition and a default value:

```php
'package_price' => [
    [
        'value'   => 5.00,
        'filters' => ['expression_language:user.getRegisterDays() >= 365', 'made_at_least_one_purchase'],
    ],
    [
        'value'   => 10.00,
    ],
],
```

<a id="voters"></a>
## Voters

The actual voters can be registered here:

```
app/config/judge.php
```

The voters contain the logic to decide whether the given filter should return true or false. This decides if either a feature is *on* or *off* or what a value should return regarding to it's config.

<a id="adapters"></a>
## Adapters

By default, Judge uses the Laravel config to read the features/values. You can choose between the **config**, **redis** and **cache** adapter.

```
app/config/judge.php
```

If you want to add you own adapter, go for it. Just implement the **AdapterInterface**.

<a id="usage"></a>
## Usage

Within your controllers, you can use this for example...

```php
$greeting = Value::decide('greeting', $this->getUser());
echo $greeting;
```

Or this:

```php
if (Feature::decide('show_memory_usage', Auth::user())) {
    echo 'Memory usage: ' . memory_get_usage();
}
```

<a id="tbd"></a>
## tbd

- Breakers
