<?php

use Orchestra\Testbench\TestCase as BaseTestCase;
use Plank\Metable\MetableServiceProvider;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/_data/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            MetableServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp($app)
    {
        date_default_timezone_set('GMT');
        //use in-memory database
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('database.default', 'testing');
    }

    protected function getPrivateProperty($class, $property_name)
    {
        $reflector = new ReflectionClass($class);
        $property = $reflector->getProperty($property_name);
        $property->setAccessible(true);

        return $property;
    }

    protected function getPrivateMethod($class, $method_name)
    {
        $reflector = new ReflectionClass($class);
        $method = $reflector->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }

    protected function useDatabase()
    {
        $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $database = $this->app['config']->get('database.default');

        //Remigrate all database tables
        $artisan->call('migrate:refresh', [
            '--database' => $database,
            '--realpath' => realpath(__DIR__.'/../migrations'),
        ]);
    }
}
