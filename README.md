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
$ids = get_cached_model_ids(MyClass::class, 60);
```
 
---

- `Permafrost\Helpers\RouteHelper`

Examples:

Assuming a route defined as `/products/{category}/{id}` and named `web.products.show`, return `/products/books/123`:
```php
    //class-based
    RouteHelper::routename('web.products.show/books/123');
    
    //helper function
    routename('web.products.show/books/123');
```

#### Helper Functions

```php
    //get all ids for a model and cache the results; returns cached results if they exist
    function get_cached_model_ids(string $modelClass, int $ttlSeconds, int $recordLimit): array;
    
    //get all values of a column for a model and cache the results; returns cached results if they exist
    function get_cached_model_columns(string $modelClass, string $column, int $ttlSeconds, int $recordLimit): array;

    //get all values of a column for a model
    function get_model_column($modelClass, string $column, int $recordLimit): array;

    //get all ids for a model
    function get_model_ids($modelClass, int $recordLimit): array;

    //truncates text with an ellipsis, but doesn't return partially truncated words
    function str_tease(string $string, int $length, string $moreTextIndicator = '...'): string;

    //returns a relative url instead of a complete url
    function relative_route(string $name, array $parameters): string;

    //returns the value of a named route along with the provided parameters (see RouteHelper class)
    function routepath(string $routepath): string;

    //returns an array of the validated data
    function validate($fields, $rules);
    
    //returns true if the data passes validation
    function validated($fields, $rules);
    
    //creates a validator instance, accepting either array or string parameters
    function validator_create($fields, $rules);
```

#### Testing

``` bash
./vendor/bin/phpunit --testdox
```

## License

The MIT License (MIT). Please see the [LICENSE File](LICENSE) for more information.
