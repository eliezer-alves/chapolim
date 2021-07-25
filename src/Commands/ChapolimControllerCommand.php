<?php

namespace Eliezer\Chapolim\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChapolimControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-controller {name} {--S|service=} {--route} {--r|resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class with service layer';

    protected $file;
    protected $routeFile;
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
        $this->routeFile = base_path('routes/api.php');
        $this->resource = $this->option('resource');
        $this->route = $this->option('route');
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
     * Returns the contents of the route file.
     *
     * @return void
     */
    private function setRouteFileContents()
    {
        $prefix = strtolower(preg_replace(["/([A-Z]+)/", "/ ([A-Z]+)([A-Z][a-z])/"], ["-$1", "_$1_$2"], lcfirst($this->name)));
        $routeDescription = preg_replace(["/([A-Z]+)/", "/ ([A-Z]+)([A-Z][a-z])/"], [" $1", "_$1_$2"], $this->name);

        $routeGroupTemplate = file_get_contents(__DIR__ . './stubs/route-group.stub');
        $routeGroupContents = str_replace('{{ routeDescription }}', $routeDescription,
            str_replace('{{ prefix }}', $prefix,
            str_replace('{{ class }}', $this->name, $routeGroupTemplate)
        ));

        $template = file_get_contents($this->routeFile);
        return str_replace("use Illuminate\Support\Facades\Route;", "use Illuminate\Support\Facades\Route;\nuse $this->namespace\\$this->name;", str_replace("use $this->namespace\\$this->name;", '', str_replace($routeGroupContents, '', $template))) . $routeGroupContents;
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
        if($this->route){
            File::put($this->routeFile, $this->setRouteFileContents());
            $this->info('Route group created successfully.');
        }
        return 0;
    }
}
