<?php

namespace Armincms\Snail\Tests;

use Orchestra\Testbench\TestCase;
use Armincms\Snail\Tests\Fixtures\PostResource;
use Armincms\Snail\Tests\Fixtures\PostResourceVersioning;
use Armincms\Snail\Snail;
use Armincms\Snail\SnailServiceProvider;
use Armincms\Snail\SnailCoreServiceProvider;

class IntegrationTest extends TestCase
{
    public $version = '1.0.0';

    public $versioning = '1.0.1';

	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
	    parent::setUp();

        $this->loadMigrations();

        $this->withFactories(__DIR__.'/Factories'); 

        Snail::resources([
            PostResource::class,
        ]);

        Snail::version($this->versioning)->resources([
            PostResourceVersioning::class,
        ]); 
	} 

	protected function getPackageAliases($app)
	{
	    return [ 
	    ];
	}

    /**
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SnailCoreServiceProvider::class,
            SnailServiceProvider::class,
            // TestServiceProvider::class,
        ];
    }

    /**
     * Define environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--path' => realpath(__DIR__.'/Migrations'),
        ]);
    }

    protected function migrate()
    {
        $this->artisan('migrate')->run();
    }

    public function test_for_ignore_warnings()
    { 
    }
}