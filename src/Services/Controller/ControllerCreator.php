<?php

namespace Eliezer\Chapolim\Services\Controller;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class ControllerCreator extends Creator
{

    public function create($name, $module = null, $service = null, $resource = false, $force = false)
    {
        if (! $force)
            $this->ensureClassDoesntAlreadyExist($name, $this->getControllerPath($module));
        
        $stub = $this->getStub($service, $resource);
        $path = $this->getPath($name, $this->getControllerPath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $module, $stub, $service)
        );

        return $path;
    }

    /**
     * Get the controller stub file.
     *
     * @param  string|null  $table
     * @param  bool  $create
     * @return string
     */
    protected function getStub($service, $resource)
    {
        if (is_null($service) && $resource) {
            $stub = $this->stubPath().'/controller.api.stub';
        } elseif (! is_null($service) && !$resource) {
            $stub = $this->stubPath().'/controller.service.plain.stub';
        } elseif (! is_null($service) && $resource) {
            $stub = $this->stubPath().'/controller.service.api.stub';
        } else {
            $stub = $this->stubPath().'/controller.plain.stub';
        }

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the controller stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $module, $stub, $service)
    {
        $stub = str_replace(
            ['DummyNamespace', '{{ namespace }}', '{{namespace}}'],
            $this->getNamespace($module), $stub
        );

        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );

        $stub = str_replace(
            ['DummyParentClass', '{{ parentClass }}', '{{parentClass}}'],
            $this->getParentClass($module), $stub
        );

        if (! is_null($service)) {
            $stub = str_replace(
                ['DummyService', '{{ service }}', '{{service}}'],
                $service, $stub
            );
        }

        return $stub;
    }    

    /**
     * Get the name of the parent class.
     *
     * @param  string  $module
     * @return string
     */
    protected function getParentClass($module)
    {
        return is_null($module) ? 'Controller' : Str::studly($module . 'Controller');
    }

    /**
     * Get the class namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module) ? 'App\Http\Controllers' : 'Modules\\' . Str::studly($module) . '\Http\Controllers';
    }

    /**
     * Get path to the controller.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getControllerPath($module)
    {
        if (! is_null($module)) {
            return base_path('modules/' . $module . '/Http/Controllers');
        }

        return app_path('Http/Controllers');
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