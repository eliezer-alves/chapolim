<?php

namespace Eliezer\Chapolim\Services\Repository;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class RepositoryInterfaceCreator extends Creator
{

    public function create($name, $module = null)
    {
        $this->ensureClassDoesntAlreadyExist($name, $this->getRepositoryInterfacePath($module));        
        
        $stub = $this->getStub();
        $path = $this->getPath($name, $this->getRepositoryInterfacePath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $module, $stub)
        );

        return $path;
    }

    /**
     * Get the repository stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->stubPath().'/repository-interface.stub';

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the model stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $module, $stub)
    {
        $stub = str_replace(
            ['DummyNamespace', '{{ namespace }}', '{{namespace}}'],
            $this->getNamespace($module), $stub
        );

        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );
        return $stub;
    }

    /**
     * Get the class name of a class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module)
            ? 'App\Repositories\Contracts'
            : 'Modules\\' . Str::studly($module) . '\Repositories\Contracts';
    }

    /**
     * Get path to the class.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getRepositoryInterfacePath($module)
    {
        if(! is_null($module)) {
            return base_path('modules/' . $module . '/Repositories/Contracts');
        }

        return app_path('Repositories/Contracts');
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
}