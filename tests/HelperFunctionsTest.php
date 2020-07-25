<?php

namespace Permafrost\Helpers\Tests;

use Illuminate\Validation\Validator;
use Orchestra\Testbench\TestCase;
use Permafrost\Helpers\Tests\Database\Example;

class HelperFunctionsTest extends TestCase
{
    /** @var \Illuminate\Routing\Router|mixed $router */
    protected $router;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->router = app('router');

        $this->router->get('/', function () {
            //
        })->name('test.zero');

        $this->router->get('/test/one', function () {
            //
        })->name('test.one');

        $this->router->get('/test/two/{id}', function () {
        })->name('test.two');

        $this->router->get('/test/three/{a}/{b}', function () {
        })->name('test.three');

        $this->router->getRoutes()->refreshNameLookups();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.driver', 'database');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @test
     */
    public function it_gets_model_columns(): void
    {
        $columns = get_model_column(Example::class, 'name');

        $this->assertSame(['helloworld'], $columns);
    }

    /**
     * @test
     */
    public function it_gets_model_ids(): void
    {
        $columns = get_model_ids(Example::class);

        $this->assertSame([1], $columns);
    }

    /**
     * @test
     */
    public function it_gets_cached_model_ids(): void
    {
        $columns = get_cached_model_ids(Example::class);

        $cache = \DB::table('cache')->where('key', 'LIKE', '%'.Example::class)->first();

        $this->assertSame([1], $columns);
        $this->assertEquals('laravel_cachemodel_column:id:'.Example::class, $cache->key);
    }

    /**
     * @test
     */
    public function it_gets_cached_model_columns(): void
    {
        $columns = get_cached_model_columns(Example::class, 'name');

        $cache = \DB::table('cache')->where('key', 'LIKE', '%'.Example::class)->first();

        $this->assertSame(['helloworld'], $columns);
        $this->assertEquals('laravel_cachemodel_column:name:'.Example::class, $cache->key);
    }

    /**
     * @test
     */
    public function it_returns_a_relative_route(): void
    {
        $results = [
            relative_route('test.zero'),
            relative_route('test.one'),
            relative_route('test.three', ['a' => 1, 'b' => 2]),
            relative_route('test.two', ['id' => 1]),
        ];

        $this->assertSame(['/', '/test/one', '/test/three/1/2', '/test/two/1'], $results);
    }

    /**
     * @test
     */
    public function it_returns_a_route_from_a_routepath(): void
    {
        $results = [
            routepath('test.one'),
            routepath('test.two/1'),
            routepath('test.three/123/456'),
        ];

        $this->assertSame([
            'http://localhost/test/one',
            'http://localhost/test/two/1',
            'http://localhost/test/three/123/456',
        ], $results);
    }

    /**
     * @test
     */
    public function it_truncates_a_string_without_abbreviating_words(): void
    {
        $results = [
            str_tease('this is a test sentence', 12),
            str_tease('this is a test sentence', 17),
            str_tease('this is a test sentence', 50),
        ];

        $this->assertSame([
            'this is a...',
            'this is a test...',
            'this is a test sentence',
        ], $results);
    }

    /**
     * @test
     */
    public function it_creates_a_validator_instance(): void
    {
        $validator = validator_create(['myfield' => 'hello'], ['myfield' => 'string|required|min:3']);

        $this->assertSame(Validator::class, get_class($validator));
    }

    /**
     * @test
     */
    public function it_validates_data(): void
    {
        $validated = validate(['myfield' => 'hello'], ['myfield' => 'string|required|min:3']);

        $this->assertEquals(['myfield' => 'hello'], $validated);
    }

    /**
     * @test
     */
    public function it_accepts_strings_as_validator_arguments(): void
    {
        $validator = validator_create('hello', 'string|required|min:3');

        $this->assertTrue($validator->passes());
    }

    /**
     * @test
     */
    public function it_validated_data_successfully(): void
    {
        $results = [
            validated(['myfield' => 'hello'], ['myfield' => 'string|required|min:3']),
            validated('test', 'string|required|min:10'),
        ];

        $this->assertEquals([true, false], $results);
    }
}
