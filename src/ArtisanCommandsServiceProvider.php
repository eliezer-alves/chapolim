<?php

namespace Eliezer\Chapolim;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;

class ArtisanCommandsServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ChapolimCommand::class,
                Commands\ControllerMakeCommand::class,
                Commands\MigrationMakeCommand::class,
                Commands\ModelMakeCommand::class,
                Commands\RepositoryMakeCommand::class,
                Commands\ServiceMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
