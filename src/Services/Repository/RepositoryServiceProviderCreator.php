<?php

namespace Eliezer\Chapolim\Services\Repository;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class RepositoryServiceProviderCreator extends Creator
{
    
    public function create($name, $module = null, $ormFolder = null)
    {
        $path = $this->optimize($name, $module, $ormFolder);

        $this->registerInAppConfig($name, $module);

        return $path;
    }

    public function optimize($name, $module = null, $ormFolder = null)
    {
        $ormFolder = Str::studly($ormFolder ?? 'Eloquent');
        $stub = $this->getStub();
        $path = $this->getPath($name, $this->getProviderPath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $module, $stub, $ormFolder)
        );

        return $path;
    }

    /**
     * Adding provider to system config file
     *
     */
    protected function registerInAppConfig($name, $module)
    {
        $useProvider = $this->getNamespace($module) . '\\' . $this->getClassName($name);
        $appConfig = $this->getAppConfig();

        $this->files->put(
            config_path('app.php'), $this->setAppConfig($useProvider, $appConfig)
        );
    } 

    /**
     * Get the repository stub file.
     *
     * @return string
     */
    protected function getStub($optimize = true)
    {
        $stub = $this->stubPath().'/repository-service-provider.stub';

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
    protected function populateStub($name, $module, $stub, $ormFolder)
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
            ['DummyNamespaceRepositoriesContracts', '{{ namespaceRepositoriesContracts }}', '{{namespaceRepositoriesContracts}}'],
            $this->getRepositoryInterfaceNamespace($module), $stub
        );

        $stub = str_replace(
            ['DummyNamespaceRepositories', '{{ namespaceRepositories }}', '{{namespaceRepositories}}'],
            $this->getRepositoryNamespace($module, $ormFolder), $stub
        );

        foreach (glob($this->getRepositoryPath($module, $ormFolder) . '/*.php') as $filename) {
            
            $repository = basename($filename, '.php');
            $repositoryInterface = $repository . 'Interface';
            $bindRepository = "\$this->app->bind(\n\t\t\t$repositoryInterface::class,\n\t\t\t$repository::class\n\t\t);";

            $stub = str_replace(
                ['//add-interface'],
                "$repositoryInterface,\n\t//add-interface", $stub
            );

            $stub = str_replace(
                ['//add-repository'],
                "$repository,\n\t//add-repository", $stub
            );

            $stub = str_replace(
                ['//add-bindRepository'],
                "$bindRepository\n\n\t\t//add-bindRepository", $stub
            );
        }

        $stub = str_replace(
            ["\n\t//add-interface", "\n\t//add-repository", "\n\t\t//add-bindRepository"],
            '', $stub
        );

        return $stub;
    }

    

    /**
     * Get the application configuration file.
     *
     * @return string
     */
    protected function getAppConfig()
    {
        return $this->files->get(config_path('app.php'));
    }

    /**
     * Set the application configuration file.
     *
     * @return string
     */
    protected function setAppConfig($useProvider, $appConfig)
    {
        $appConfig = str_replace(
            ['App\Providers\RouteServiceProvider::class,'],
            "App\Providers\RouteServiceProvider::class,\n\t\t$useProvider::class,", $appConfig
        );

        return $appConfig;
    }

    /**
     * Get the class namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module)
            ? 'App\Providers'
            : 'Modules\\' . Str::studly($module) . '\Providers';
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
            : 'Modules\\' . Str::studly($module) . '\Repositories' . $ormFolder;
    }
    
    /**
     * Get path to the class.
     *
     * @param  string  $module
     * @return string
     */
    protected function getProviderPath($module)
    {
        if (! is_null($module)) {
            return base_path('modules/' . $module . '/Providers');
        }

        return app_path('Providers');
    }

    /**
     * Get repository classes path.
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