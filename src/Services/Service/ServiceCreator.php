<?php

namespace Eliezer\Chapolim\Services\Service;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class ServiceCreator extends Creator
{

    public function create($name, $module = null, $repository = null, $resource = false)
    {
        $this->ensureClassDoesntAlreadyExist($name, $this->getServicePath($module));
        
        $stub = $this->getStub($repository, $resource);
        $path = $this->getPath($name, $this->getservicePath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $module, $stub, $repository)
        );

        return $path;
    }

    /**
     * Get the service stub file.
     *
     * @param  string|null  $table
     * @param  bool  $create
     * @return string
     */
    protected function getStub($repository, $resource)
    {
        if (is_null($repository) && $resource) {
            $stub = $this->stubPath().'/service.stub';
        } elseif (! is_null($repository) && !$resource) {
            $stub = $this->stubPath().'/service.repository.plain.stub';
        } elseif (! is_null($repository) && $resource) {
            $stub = $this->stubPath().'/service.repository.stub';
        } else {
            $stub = $this->stubPath().'/service.plain.stub';
        }

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the service stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $module, $stub, $repository)
    {
        $stub = str_replace(
            ['DummyNamespace', '{{ namespace }}', '{{namespace}}'],
            $this->getNamespace($module), $stub
        );

        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );

        if (! is_null($repository)) {
            $stub = str_replace(
                ['DummyRepositoryInterface', '{{ repositoryInterface }}', '{{repositoryInterface}}'],
                $this->getRepositoryInterface($repository), $stub
            );

            $stub = str_replace(
                ['DummyAttributeRepository', '{{ attributeRepository }}', '{{attributeRepository}}'],
                $this->getAttributeRepository($repository), $stub
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
        return is_null($module) ? 'service' : Str::studly($module . 'service');
    }

    /**
     * Get the class name of a class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module) ? 'App\Services' : 'Modules\\' . Str::studly($module) . '\Services';
    }

    /**
     * Get the class interface name of a repository.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRepositoryInterface($repository)
    {
        return is_null($repository) ? '' : Str::studly($repository) . 'Interface';
    }

    /**
     * Get the repository attribute name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getAttributeRepository($repository)
    {
        return is_null($repository) ? '' : str_replace('RepositoryRepository', 'Repository', lcfirst(Str::studly($repository)) . 'Repository');
    }

    /**
     * Get path to the service.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getServicePath($module)
    {
        if($module) {
            return base_path('modules/' . $module . '/Services');
        }

        return app_path('Services');
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