<?php

namespace Armincms\Snail\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ResourceCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'snail:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new schema class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

    /**
     * A list of schema names which are protected.
     *
     * @var array
     */
    protected $protectedNames = [ 
    ]; 

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = $this->option('model');

        if (is_null($model)) {
            $model = $this->laravel->getNamespace().str_replace('/', '\\', $this->argument('name'));
        } elseif (! Str::startsWith($model, [
            $this->laravel->getNamespace(), '\\',
        ])) {
            $model = $this->laravel->getNamespace().$model;
        }

        $schemaName = $this->argument('name');

        if (in_array(strtolower($schemaName), $this->protectedNames)) {
            $this->warn("You *must* override the uriKey method for your {$schemaName} schema.");
        }

        return str_replace(
            'DummyFullModel', $model, parent::buildClass($name)
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/schema.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Snail';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model class being represented.'],
        ];
    }
}
