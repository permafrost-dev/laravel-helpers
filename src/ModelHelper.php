<?php

namespace Permafrost\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModelHelper
{
    /** @var false|\Illuminate\Database\Eloquent\Builder|string|mixed $modelClass */
    protected $modelClass;

    /** @var \Illuminate\Database\Eloquent\Builder $queryBuilder */
    protected $queryBuilder = null;

    /** @var bool $cached */
    protected $cached = false;

    /** @var int $cachedTtl */
    protected $cachedTtl = 30;

    /** @var string $cacheIdentifier */
    protected $cacheIdentifier = '';

    public function __construct($modelClass)
    {
        if ($modelClass instanceof Builder) {
            $this->queryBuilder = $modelClass;
        }

        if ($modelClass instanceof Model) {
            $this->queryBuilder = $modelClass::query();
            $modelClass = get_class($modelClass);
        }

        if ($this->queryBuilder === null) {
            $this->queryBuilder = $modelClass::query();
        }

        $this->modelClass = $modelClass;
    }

    /**
     * Proxies method calls to the underlying Query Builder instance, optionally caching the result.
     *
     * @param $method
     * @param $params
     *
     * @return $this
     */
    public function __call($method, $params)
    {
        $result = $this->queryBuilder->$method(...$params);

        if ($result instanceof Builder) {
            $this->queryBuilder = $result;

            return $this;
        }

        if (!$this->cached) {
            return $result;
        }

        $cacheKey = 'models:'.$this->cacheIdentifier;

        return Cache::remember($cacheKey, $this->cachedTtl, static function () use ($result) {
            return $result;
        });
    }

    /**
     * Creates an instance of the class.
     *
     * @param $modelClass
     *
     * @return \Permafrost\Helpers\ModelHelper
     */
    public static function create($modelClass): ModelHelper
    {
        return new self($modelClass);
    }

    /**
     * Retrieves the values from model's specified attribute, optionally caching the result.
     *
     * @param string $column
     *
     * @return array
     */
    public function column(string $column): array
    {
        if (!$this->cached) {
            return $this->getColumn($column);
        }

        $cacheKey = "model_column:${column}:".$this->cacheIdentifier;

        return Cache::remember($cacheKey, $this->cachedTtl, function () use ($column) {
            return $this->getColumn($column);
        });
    }

    /**
     * Enables result caching in a fluent manner.
     *
     * @param int $ttlSeconds
     *
     * @return $this
     */
    public function cached(int $ttlSeconds = 30): ModelHelper
    {
        $this->cached = true;
        $this->cachedTtl = $ttlSeconds;
        $this->cacheIdentifier = $this->getCacheIdentifier();

        return $this;
    }

    /**
     * Returns a model's id values.
     *
     * @return array
     */
    public function ids(): array
    {
        return $this->column('id');
    }

    /**
     * Performs the actual database query and returns the column values as an array.
     *
     * @param string $column
     *
     * @return array
     */
    protected function getColumn(string $column): array
    {
        return $this->queryBuilder
            ->orderBy($column)
            ->get($column)
            ->pluck($column)
            ->all();
    }

    /**
     * Returns a unique identifier for the cache key when result caching is used.
     *
     * @return string
     */
    protected function getCacheIdentifier(): string
    {
        return is_string($this->modelClass)
            ? $this->modelClass
            : $this->queryBuilder->make()->getTable();
    }
}
