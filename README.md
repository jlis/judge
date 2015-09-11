# Judge - A feature and value decider for Laravel 5

### The easy way to toggle/decide features and values.

- [Installation](#installation).
- [Feature configuration](#features).
- [Feature configuration examples](#featuresExamples).
- [Value configuration](#values).
- [Value configuration examples](#valuesExamples).
- [Voters](#voters).
- [Usage](#usage).


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

Note that the default value of a feature, if not defined otherwise, is always false.

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

A feature with multiple filters chained in an AND condition:

```php
'enable_captcha' => [
    [
        'value'   => true,
        'filters' => ['env:production', 'expression_language:user==null'],
    ],
],
```

A feature with multiple filters chained in an OR condition:

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

A feature with a negated filter:

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
'hello_message' => [
    [
        'value'   => 'Hello darling <3',
        'filters' => ['expression_language:user.getName()=="Girlfriend"'],
    ],
    [
        'value' => 'Hello there.',
    ],
]
```

This name of the value is "hello_message". It should return "Hello %s" if the expression voter return true (assuming the given user is not NULL). Otherwise it should return "Hello guest."

(*ExpressionVoter.php* check with the help of the Symfony Expression Language if the given expression is true)

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

A value with multiple filters chained in an AND condition and a default value:

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
app/config/voters.php
```

The voters contain the logic to decide, if the given filter should return true or false. This decides either a feature is on or off or what a value should return regarding to his config.

## Usage

Within your controllers, you can use this for example...

```php
$helloMessage = Value::decide('hello_message', $this->getUser());
echo $helloMessage;
```

Or this for example:

```php
if (Feature::decide('show_memory_usage', Auth::user())) {
    echo 'Memory usage: ' . memory_get_usage();
}
```

