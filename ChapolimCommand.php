<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ChapolimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make {name} {--m|model} {--c|controller} {--R|repository} {--S|service} {--r|resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates project scaffold with all layers';

    protected $model;
    protected $repository;
    protected $service;
    protected $controller;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->hydrator();
        if($this->option('model')){
            Artisan::call("make:model", ['name' => $this->model]);
            $this->info('Model created successfully.');
        }
        if($this->option('controller')){
            Artisan::call("chapolim:controller", [
                'name' => $this->controller,
                '-r' => 'default'
            ]);
            $this->info('Controller created successfully.');
        }
        if($this->option('repository')){
            Artisan::call("make:repository", [
                'name' => $this->repository,
                '-m' => $this->model
            ]);
            $this->info('Repository created successfully.');
        }
        if($this->option('service')){
            Artisan::call("make:service", [
                'name' => $this->service,
                '-R' => $this->repository,
                '-r' => 'default'
            ]);
            $this->info('Service created successfully.');
        }
        return 0;
    }
}
