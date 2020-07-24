<?php

// helpers

if (!function_exists('get_model_column')) {
    /**
     * Get the column values for a model, optionally providing a limit to the
     * number of total records retrieved, or -1 to retrive all records.
     *
     * @param mixed $modelClass
     * @param string $column
     * @param int $recordLimit
     *
     * @return array
     */
    function get_model_column($modelClass, string $column, int $recordLimit = -1): array
    {
        return $modelClass::query()
            ->when($limit > -1, function($query) use ($recordLimit) {
                return $query->limit($recordLimit);
            })
            ->orderBy($column)
            ->get($column)
            ->pluck($column)
            ->all();
    }
}

if (!function_exists('get_model_ids')) {

    /**
     * Get the 'id' column values for a model, optionally providing a limit to the
     * number of total records retrieved, or -1 to retrive all records.
     *
     * @param mixed $modelClass
     * @param int $recordLimit
     *
     * @see \get_model_column()
     *
     * @return array
     */
    function get_model_ids($class, int $recordLimit = -1)
    {
        return get_model_column($class, 'id', $recordLimit);
    }
}

if (!function_exists('get_cached_model_ids')) {
    /**
     * Return an array of a model's id values, using cached values if available.
     *
     * @param string $class
     * @param int $ttlSeconds
     * @param int $recordLimit
     * @param string|null $cacheStore
     * @return mixed
     *
     * @throws \Exception
     */
    function get_cached_model_ids(string $class, int $ttlSeconds = 30, int $recordLimit = -1, ?string $cacheStore = null)
    {
        $cache = empty($store) ? cache() : $cache->store($store);
        
        return $cache->remember('class_ids:'.$class, $ttlSeconds, static function () use ($class, $ttlSeconds, $recordLimit) {
            return get_model_ids($class, $ttlSeconds, $recordLimit);
        });
    }
}