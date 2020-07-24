<?php

namespace Permafrost\Helpers\Tests;

//use PHPUnit\Framework\TestCase;
use Orchestra\Testbench\TestCase;
use Permafrost\Helpers\ModelHelper;
use Permafrost\Helpers\Tests\Database\Example;

class ModelHelperTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.driver', 'database');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

//    /**
//     * @test
//     */
//    public function it_gets_example_from_db(): void
//    {
//        $example = \DB::table('examples')->where('id', '=', 1)->first();
//
//        $this->assertEquals('helloworld', $example->name);
//
//        $this->assertEquals([
//            'id',
//            'name',
//            'created_at',
//            'updated_at',
//        ], \Schema::getColumnListing('examples'));
//    }

    /**
     * @test
     */
    public function it_caches_ids_successfully(): void
    {
        $ids = ModelHelper::create(Example::class)
            ->cached()
            ->ids();

        $cache = \DB::table('cache')->where('key', 'LIKE', '%'.Example::class)->first();

        $this->assertEquals([1], $ids);
        $this->assertEquals('laravel_cachemodel_column:id:' . Example::class, $cache->key);
    }

    /**
     * @test
     */
    public function it_caches_columns_successfully(): void
    {
        $names = ModelHelper::create(Example::class)
            ->cached()
            ->column('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%')->first();

        $this->assertEquals(['helloworld'], $names);
        $this->assertEquals('laravel_cachemodel_column:name:' . Example::class, $cache->key);
    }

    /**
     * @test
     */
    public function it_gets_columns_successfully_without_caching(): void
    {
        $names = ModelHelper::create(Example::class)
            ->column('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%')->first();

        $this->assertEquals(['helloworld'], $names);
        $this->assertEmpty($cache);
    }

    /**
     * @test
     */
    public function it_accepts_a_model_when_creating_helper(): void
    {
        $model = Example::query()->first();
        $names = ModelHelper::create($model)
            ->cached()
            ->column('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%'.Example::class)->first();

        $this->assertEquals(['helloworld'], $names);
        $this->assertEquals('laravel_cachemodel_column:name:' . Example::class, $cache->key);
    }

    /**
     * @test
     */
    public function it_accepts_a_query_builder_instance_when_creating_helper_and_uses_table_name_as_cache_key(): void
    {
        $builder = Example::query();
        $names = ModelHelper::create($builder)
            ->cached()
            ->column('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%')->first();

        $this->assertEquals(['helloworld'], $names);
        $this->assertEquals('laravel_cachemodel_column:name:examples', $cache->key);
    }

    /**
     * @test
     */
    public function it_allows_calling_query_builder_methods_when_cached(): void
    {
        $builder = Example::query();
        $names = ModelHelper::create($builder)
            ->cached()
            ->limit(1)
            ->column('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%')->first();

        $this->assertEquals(['helloworld'], $names);
        $this->assertEquals('laravel_cachemodel_column:name:examples', $cache->key);
    }

    /**
     * @test
     */
    public function it_allows_calling_query_builder_methods(): void
    {
        $builder = Example::query();
        $names = ModelHelper::create($builder)
            ->limit(1)
            ->get('name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%')->first();

        $this->assertEquals(['helloworld'], $names->pluck('name')->all());
        $this->assertEmpty($cache);
    }

    /**
     * @test
     */
    public function it_caches_models_successfully(): void
    {
        $models = ModelHelper::create(Example::class)
            ->cached()
            ->get();

        $cache = \DB::table('cache')->where('key', 'LIKE', '%'.Example::class)->first();

        $this->assertEquals(Example::all(), $models);
        $this->assertEquals('laravel_cachemodels:' . Example::class, $cache->key);
    }
}
