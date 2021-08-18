<?php

namespace Eliezer\Chapolim\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ChapolimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make
        {name : The base name of the all classes.}
        {--module= : The application module.}
        {--m|model : Generates a model class.}
        {--M|migration : Generates a migration class.}
        {--c|controller : Generates a controller class.}
        {--R|repository : Generates a repository class.}
        {--S|service : Generates a service class.}
        {--a|all : Generates the classes of all layers}
        {--r|resource : Generate a resource in controller and service classes.}
        {--route : Generates a group of routes referring to controller resources in the api route file.}
        {--fillable= : The fillable attribute of the model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates project scaffold with all layers';

    protected $all;
    protected $module;
    protected $model;
    protected $migration;
    protected $repository;
    protected $service;
    protected $controller;
    protected $resource;
    protected $route;
    protected $fillable;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Hydrate class parameters.
     *
     * @return void
     */

    private function hydrator()
    {
        // dd($this->options());
        $this->module = $this->option('module');
        $this->fillable = $this->option('fillable');
        $this->resource = $this->option('resource');
        $this->route = $this->option('route');
        if ($this->all || !($this->option('model') && $this->option('migration') && $this->option('controller') && $this->option('repository') && $this->option('service'))) {
            $this->model = Str::studly($this->argument('name'));
            $this->migration = Str::studly('Create' . $this->argument('name') . 'Table');
            $this->controller = Str::studly($this->argument('name') . 'Controller');
            $this->repository = Str::studly($this->argument('name') . 'Repository');
            $this->service = Str::studly($this->argument('name') . 'Service');
            $this->all = true;
        } else {
            if ($this->option('model')) {
                $this->model = Str::studly($this->argument('name'));
            }
            if ($this->option('model')) {
                $this->migration = Str::studly('Create' . $this->argument('name') . 'Table');
            }
            if ($this->option('controller')) {
                $this->controller = Str::studly($this->argument('name') . 'Controller');
            }
            if ($this->option('repository')) {
                $this->repository = Str::studly($this->argument('name') . 'Repository');
            }
            if ($this->option('service')) {
                $this->service = Str::studly($this->argument('name') . 'Service');
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->hydrator();
        if ($this->all || $this->option('model')) {
            Artisan::call("chapolim:make-model", [
                'name' => $this->model,
                '--module' => $this->module,
                '--fillable' => $this->fillable,
            ]);
            $this->info('Model created successfully.');
        }
        if ($this->all || $this->option('migration')) {
            Artisan::call("chapolim:make-migration", [
                'name' => $this->migration,
                '--module' => $this->module,
            ]);
            $this->info('Migration created successfully.');
        }
        if ($this->all || $this->option('repository')) {
            Artisan::call("chapolim:make-repository", [
                'name' => $this->repository,
                '--module' => $this->module,
                '-m' => $this->model,
            ]);
            $this->info('Repository created successfully.');
        }
        if ($this->all || $this->option('service')) {
            Artisan::call("chapolim:make-service", [
                'name' => $this->service,
                '--module' => $this->module,
                '-R' => $this->repository,
                '-r' => $this->resource,
            ]);
            $this->info('Service created successfully.');
        }
        if ($this->all || $this->option('controller')) {
            Artisan::call("chapolim:make-controller", [
                'name' => $this->controller,
                '--module' => $this->module,
                '-r' => $this->resource,
                '--route' => $this->route,
            ]);
            $this->info('Controller created successfully.');
            if ($this->route) {
                $this->info('Route group created successfully.');
            }
        }

        return 0;
    }
}