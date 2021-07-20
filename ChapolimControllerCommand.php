<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChapolimControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:controller {name} {--S|service=} {--r|resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class with service layer';

    protected $file;
    protected $path;
    protected $name;
    protected $namespace;
    protected $resource;
    protected $service;

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
        $this->name = $this->argument('name');
        $this->namespace = 'App\Http\Controllers';
        $this->resource = $this->option('resource');
        $this->service = str_replace('Controller', '', $this->option('service') ?? $this->name . 'Service');
        $this->path = app_path("Http/Controllers");
        $this->file = "$this->path/$this->name.php";
    }

    /**
     * Returns the contents of the file to be created.
     *
     * @return void
     */
    private function setContents()
    {
        if($this->resource){
            $template = file_get_contents(__DIR__ . './stubs/controller.service.api.stub');
        }else{
            $template = file_get_contents(__DIR__ . './stubs/controller.service.plain.stub');
        }

        return str_replace('{{ namespace }}', $this->namespace,
            str_replace('{{ class }}', $this->name,
            str_replace('{{ service }}', $this->service, $template)
        ));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->hydrator();
        File::put($this->file, $this->setContents());        

        $this->info('Controller created successfully.');
        return 0;
    }
}
