<?php

use Permafrost\Helpers\ModelHelper;
use Permafrost\Helpers\RouteHelper;

if (!function_exists('get_cached_model_ids')) {
    /**
     * Return an array of a model's id values, using cached values if available.
     *
     * @throws \Exception
     *
     * @return array
     */
    function get_cached_model_ids(string $modelClass, int $ttlSeconds = 30, int $recordLimit = -1): array
    {
        return ModelHelper::create($modelClass)
            ->cached($ttlSeconds)
            ->limit($recordLimit)
            ->ids();
    }
}

if (!function_exists('get_cached_model_columns')) {
    /**
     * Return an array of a model's column values, using cached values if available.
     *
     * @return array
     *
     * @throws \Exception
     */
    function get_cached_model_columns(string $modelClass, string $column, int $ttlSeconds = 30, int $recordLimit = -1): array
    {
        return ModelHelper::create($modelClass)
            ->cached($ttlSeconds)
            ->limit($recordLimit)
            ->column($column);
    }
}

if (!function_exists('get_model_column')) {
    /**
     * Get the column values for a model, optionally providing a limit to the
     * number of total records retrieved, or -1 to retrive all records.
     *
     * @param mixed $modelClass
     *
     * @return array
     */
    function get_model_column($modelClass, string $column, int $recordLimit = -1): array
    {
        return ModelHelper::create($modelClass)
            ->limit($recordLimit)
            ->column($column);
    }
}

if (!function_exists('get_model_ids')) {
    /**
     * Get the 'id' column values for a model, optionally providing a limit to the
     * number of total records retrieved, or -1 to retrive all records.
     *
     * @see \get_model_column()
     *
     * @param mixed $modelClass
     *
     * @return array
     */
    function get_model_ids($modelClass, int $recordLimit = -1): array
    {
        return ModelHelper::create($modelClass)
            ->limit($recordLimit)
            ->ids();
    }
}

if (!function_exists('str_tease')) {
    /**
     * Taken from https://github.com/spatie/blender/blob/master/app/helpers.php.
     *
     * Shortens a string in a pretty way. It will clean it by trimming
     * it, remove all double spaces and html. If the string is then still
     * longer than the specified $length it will be shortened. The end
     * of the string is always a full word concatenated with the
     * specified moreTextIndicator.
     *
     * @param string $string
     * @param int $length
     * @param string $moreTextIndicator
     *
     * @return string
     */
    function str_tease(string $string, int $length = 200, string $moreTextIndicator = '...'): string
    {
        $string = trim($string);
        $string = strip_tags($string); //remove html
        $string = preg_replace('/\s+/', ' ', $string); //replace multiple spaces

        if (empty($string) || strlen($string) <= $length) {
            return $string;
        }

        $wrapped = wordwrap($string, $length, "\n");

        return substr($wrapped, 0, strpos($wrapped, "\n")).$moreTextIndicator;
    }
}

if (!function_exists('relative_route')) {
    /**
     * Returns a relative URL for a named route.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return string
     */
    function relative_route(string $name, array $parameters = []): string
    {
        return RouteHelper::relative($name, $parameters);
    }
}

if (!function_exists('routepath')) {
    /**
     * Returns the named route url specified by $routepath.
     *
     * When the route name is followed by '/value', the route parameters are populated using the segments,
     * each separated by a '/'.
     *
     * The number of segments provided must be equal to the number of route parameters defined in the route.
     *
     * For example:
     *          routepath('users.profile.show/123') returns
     *          route('users.profile.show', ['profileid' => 123])
     *
     *          routepath('web.products.show/books/123') returns
     *          route('web.products.show', ['category' => 'books', 'id' => 123])
     *
     *          routepath('web.products.index') returns
     *          route('web.products.index')
     *
     * @param string $routepath
     *
     * @return string
     */
    function routepath(string $routepath): string
    {
        return RouteHelper::routepath($routepath);
    }
}
