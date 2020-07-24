## Laravel Helpers

description

#### Installation

You can install the package via composer:

```bash
composer require permafrost-dev/laravel-helpers
```

#### Helper Classes

- `\Permafrost\Helpers\ModelHelper`

Examples:

- _Retrieve the first 10 models, and cache the results for 30 seconds. Repeated calls within 30 seconds will return the cached results instead of performing a new database query._
```php
$models = ModelHelper::create(MyClass::class)
    ->cached(30)
    ->orderBy('id')
    ->latest()
    ->get();
```

- _Retrieve all MyClass record id values and cache them for 60 seconds.  This can be used during database seeding to siginificantly speed up the process._
```php
// use the model helper class
$ids = ModelHelper::create(MyClass::class)
    ->cached(60)
    ->ids();

// or use a helper function
$ids = get_cached_model_ids(MyClass::class, 60, -1);
``` 

#### Helper Functions

```php
    function get_cached_model_ids(string $modelClass, int $ttlSeconds, int $recordLimit): array;

    function get_model_column($modelClass, string $column, int $recordLimit): array;

    function get_model_ids($modelClass, int $recordLimit): array;
```

#### Testing

``` bash
./vendor/bin/phpunit --testdox
```

## License

The MIT License (MIT). Please see the [LICENSE File](LICENSE) for more information.
