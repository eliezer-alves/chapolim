<?php

namespace Eliezer\Chapolim\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ChapolimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make {name} {--m|model} {--c|controller} {--R|repository} {--S|service} {--a|all} {--r|resource} {--route} {--F|fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates project scaffold with all layers';

    protected $all;
    protected $model;
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
        $this->fillable = $this->option('fillable');
        $this->resource = $this->option('resource');
        $this->route = $this->option('resource');
        if($this->all || ($this->option('model') && $this->option('controller') && $this->option('repository') && $this->option('service'))){
            $this->model = $this->argument('name');
            $this->controller = $this->argument('name') . 'Controller';
            $this->repository = $this->argument('name') . 'Repository';
            $this->service = $this->argument('name') . 'Service';
            $this->all = true;
        }else{
            if($this->option('model')){
                $this->model = $this->argument('name');
            }
            if($this->option('controller')){
                $this->controller = $this->argument('name') . 'Controller';
            }
            if($this->option('repository')){
                $this->repository = $this->argument('name') . 'Repository';
            }
            if($this->option('service')){
                $this->service = $this->argument('name') . 'Service';
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
        if($this->all || $this->option('model')){
            Artisan::call("chapolim:model", [
                'name' => $this->model,
                '-F' => $this->fillable
            ]);
            $this->info('Model created successfully.');
        }
        if($this->all || $this->option('controller')){
            Artisan::call("chapolim:controller", [
                'name' => $this->controller,
                '-r' => $this->resource,
                '--route' => $this->route
            ]);
            $this->info('Controller created successfully.');
            if($this->route){
                $this->info('Route group created successfully.');
            }
        }
        if($this->all || $this->option('repository')){
            Artisan::call("chapolim:repository", [
                'name' => $this->repository,
                '-m' => $this->model
            ]);
            $this->info('Repository created successfully.');
        }
        if($this->all || $this->option('service')){
            Artisan::call("chapolim:service", [
                'name' => $this->service,
                '-R' => $this->repository,
                '-r' => $this->resource
            ]);
            $this->info('Service created successfully.');
        }
        return 0;
    }
}