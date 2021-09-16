<?php

namespace Eliezer\Chapolim\Services\Repository;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class RepositoryLayerCreator extends Creator
{

    public function create($module = null, $ormFolder = null, $force = false)
    {
        $name = 'AbstractRepository';
        $ormFolder = Str::studly($ormFolder ?? 'Eloquent');

        if (! $force)
            $this->ensureClassDoesntAlreadyExist($name, $this->getRepositoryPath($module, $ormFolder));
        
        // make Abstract Repository Interface
        $stub = $this->getRepositoryInterfaceStub();
        $path = $this->getPath($name . 'Interface', $this->getRepositoryPath($module, 'Contracts'));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($module, $ormFolder, $stub)
        );
        
        // make Abstract Repository
        $stub = $this->getRepositoryStub();
        $path = $this->getPath($name, $this->getRepositoryPath($module, $ormFolder));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($module, $ormFolder, $stub)
        );

        return $path;
    }

    /**
     * Get the repository interface stub file.
     *
     * @return string
     */
    protected function getRepositoryInterfaceStub()
    {  
        $stub = $this->stubPath().'/abstract-repository-interface.stub';

        return $this->files->get($stub);
    }

    /**
     * Get the repository stub file.
     *
     * @return string
     */
    protected function getRepositoryStub()
    {  
        $stub = $this->stubPath().'/abstract-repository.stub';

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
    protected function populateStub($module, $ormFolder, $stub)
    {
        $stub = str_replace(
            ['DummyInterfaceNamespace', '{{ interfaceNamespace }}', '{{interfaceNamespace}}'],
            $this->getRepositoryInterfaceNamespace($module, $ormFolder), $stub
        );

        $stub = str_replace(
            ['DummyNamespace', '{{ namespace }}', '{{namespace}}'],
            $this->getRepositoryNamespace($module, $ormFolder), $stub
        );

        return $stub;
    }

    /**
     * Get the namespace of repository class interface.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRepositoryInterfaceNamespace($module)
    {
        return is_null($module)
            ? 'App\Repositories\Contracts'
            : 'Modules\\' . Str::studly($module) . '\Repositories\Contracts';
    }

    /**
     * Get the namespace of repository class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRepositoryNamespace($module, $ormFolder)
    {
        return is_null($module)
            ? 'App\Repositories\\' . $ormFolder
            : 'Modules\\' . Str::studly($module) . '\Repositories\\' . $ormFolder;
    }

    /**
     * Get path to the class.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getRepositoryPath($module, $ormFolder)
    {
        if (! is_null($module)) {
            return base_path('modules/' . $module . '/Repositories\/' . $ormFolder);
        }

        return app_path('Repositories/' . $ormFolder);
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