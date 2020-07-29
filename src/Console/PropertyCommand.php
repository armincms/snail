<?php

namespace Armincms\Snail\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class PropertyCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'snail:property';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new property class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Property';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Property.php replacements...
        $this->replace('{{ namespace }}', $this->propertyNamespace(), $path);
        $this->replace('{{ class }}', $this->propertyClass(), $path);
        $this->replace('{{ type }}', $this->propertyType(), $path);
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replace($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    { 
        return __DIR__.'/stubs/property.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return rtrim($rootNamespace, '\\').'\Snail\Properties';
    }

    /**
     * Get the property's namespace.
     *
     * @return string
     */
    protected function propertyNamespace()
    {
        return Str::studly($this->getDefaultNamespace($this->rootNamespace()));
    }

    /**
     * Get the property's escaped namespace.
     *
     * @return string
     */
    protected function escapedPropertyNamespace()
    {
        return str_replace('\\', '\\\\', $this->propertyNamespace());
    }

    /**
     * Get the property's class name.
     *
     * @return string
     */
    protected function propertyClass()
    {
        return Str::studly($this->propertyName());
    } 

    /**
     * Get the property's base name.
     *
     * @return string
     */
    protected function propertyName()
    {
        return $this->argument('name');
    }

    /**
     * Get the property's type.
     *
     * @return string
     */
    protected function propertyType()
    {  
        if($this->option('boolean')) {
            return 'AsBoolean';
        } 

        if($this->option('array')) {
            return 'AsArray';
        } 
        
        if($this->option('integer')) {
            return 'AsInteger';
        } 
        
        if($this->option('number')) {
            return 'AsNumber';
        } 
        
        if($this->option('object')) {
            return 'AsObject';
        }  

        return 'AsString';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['boolean', null, InputOption::VALUE_NONE, 'Indicates if the generated property should be a boolean property'],
            ['array', null, InputOption::VALUE_NONE, 'Indicates if the generated property should be a array property'],
            ['integer', null, InputOption::VALUE_NONE, 'Indicates if the generated property should be a integer property'],
            ['object', null, InputOption::VALUE_NONE, 'Indicates if the generated property should be a object property'],
            ['number', null, InputOption::VALUE_NONE, 'Indicates if the generated property should be a number property'], 
        ];
    }
}
