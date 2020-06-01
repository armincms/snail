<?php

namespace Armincms\Snail\Tests;

use Orchestra\Testbench\TestCase;

class IntegrationTest extends TestCase
{
	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
	    parent::setUp();

        $this->loadMigrations();

        $this->withFactories(__DIR__.'/Factories'); 
	}

	protected function getPackageProviders($app)
	{
	    return [
	    	'Armincms\\Snail\\SnailCoreServiceProvider'
	    ];
	}

	protected function getPackageAliases($app)
	{
	    return [ 
	    ];
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
	    // Setup default database to use sqlite :memory:
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
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
}