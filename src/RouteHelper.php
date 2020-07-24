<?php

namespace Permafrost\Helpers;

class RouteHelper
{
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
    public static function routepath(string $routepath): string
    {
        if (strpos($routepath, '/') === false) {
            return route($routepath);
        }

        $parts = explode('/', $routepath);
        $name = array_shift($parts);
        $routes = app('router')->getRoutes();

        $parameterNames = optional($routes->getByName($name))->parameterNames() ?? [];

        return route($name, array_combine($parameterNames, $parts));
    }

    /**
     * Returns a relative URL for a named route.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return string
     */
    public static function relative(string $name, array $parameters = []): string
    {
        $route = route($name, $parameters);
        $start = strpos($route, '://');

        $end = strpos($route, '/', $start !== false ? $start + 4 : 0);
        $result = $end !== false ? substr($route, $end) : '/';

        return empty($result) ? '/' : $result;
    }

}
