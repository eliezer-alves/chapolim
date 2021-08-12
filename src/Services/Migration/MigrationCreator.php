<?php

namespace Eliezer\Chapolim\Services\Migration;

use Eliezer\Chapolim\Services\Creator;
use InvalidArgumentException;

class MigrationCreator extends Creator
{

    public function create($name, $module = null, $table = null, $create = false)
    {
        $this->ensureClassDoesntAlreadyExist($name, $this->getMigrationPath($module));
        
        $stub = $this->getStub($table, $create);        
        $path = $this->getPath($name, $this->getMigrationPath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $stub, $table)
        );

        return $path;
    }

    /**
     * Make sure the class does not already exist.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureClassDoesntAlreadyExist($name, $path = null)
    {
        if (! empty($path)) {
            $files = $this->files->glob($path.'/*.php');

            foreach ($files as $file) {
                $this->files->requireOnce($file);
            }
        }

        if (class_exists($className = $this->getClassName($name))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Get the migration stub file.
     *
     * @param  string|null  $table
     * @param  bool  $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        if (is_null($table)) {
            $stub = $this->stubPath().'/migration.stub';
        } elseif ($create) {
            $stub = $this->stubPath().'/migration.create.stub';
        } else {
            $stub = $this->stubPath().'/migration.update.stub';
        }

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        return $stub;
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getMigrationPath($module)
    {
        if(! is_null($module)){
            return base_path('modules/' . $module . '/Database/migrations');
        }

        return database_path('migrations');
    }
    
    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

}