<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChapolimRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:repository {name} {--m|model=} {--p|path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    protected $namespace;
    protected $class;
    protected $interface;
    protected $model;
    protected $file;
    protected $interfaceFile;
    protected $path;
    protected $contractsPath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->namespace = 'App\Repositories';
    }

    /**
     * Hydrate class parameters.
     *
     * @return void
     */
    private function hydrator():void
    {
        $this->namespace .= "\\" . ($this->option('path') ?? 'Eloquent');
        $this->class = $this->argument('name');
        $this->interface = $this->class.'Interface';
        $this->model = $this->option('model');
        $this->path = app_path('Repositories/' . ($this->option('path') ?? 'Eloquent'));
        $this->contractsPath = app_path('Repositories\Contracts');
        $this->file = "$this->path/$this->class.php";
        $this->interfaceFile = "$this->contractsPath/$this->interface.php";
    }

    /**
     * Returns the contents of the file to be created.
     *
     * @return void
     */
    private function setContents()
    {
        $template = file_get_contents(__DIR__ . './stubs/repository.stub');
        if($this->model){
            $template = file_get_contents(__DIR__ . './stubs/repository.model.stub');
        }

        return str_replace('{{ namespace }}', $this->namespace,
            str_replace('{{ class }}', $this->class,
            str_replace('{{ interface }}', $this->interface,
            str_replace('{{ model }}', $this->model, $template)
        )));
    }

    /**
     * Returns the contents of the interface file to be created.
     *
     * @return void
     */
    private function setContentsInterfaceFile()
    {
        $template = file_get_contents(__DIR__ . './stubs/repository-interface.stub');

        return str_replace('{{ namespace }}', 'App\Repositories\Contracts', str_replace('{{ class }}', $this->class, $template));
    }

    /**
     * Update the repository provider file.
     *
     * @return void
     */
    private function setContentsProviderFile()
    {
        $template = file_get_contents(__DIR__ . './stubs/repository-service-provider.stub');

        foreach(glob("$this->path/*") as $filename){
            $class = basename($filename, '.php');
            $interface = $class . 'Interface';
            $template = str_replace('//add-interface', "$interface,\n\t//add-interface",
                str_replace('//add-repository', "$class,\n\t//add-repository",
                str_replace('//add-bind', "\$this->app->bind(\n\t\t\t$interface::class,\n\t\t\t$class::class\n\t\t\n\t\t);\n\t\t//add-bind", $template)
            ));
        }

        return $template;
    }

    /**
     * Register repository class with its interface
     *
     */
    private function register()
    {
        if(!File::exists(app_path('Providers/RepositoryServiceProvider.php'))){
            File::put(app_path('Providers/RepositoryServiceProvider.php'), "");
            File::put(config_path('app.php'), str_replace('App\Providers\RouteServiceProvider::class,', "App\Providers\RouteServiceProvider::class,\n\t\tApp\Providers\RepositoryServiceProvider::class,\n", file_get_contents(config_path('app.php'))));
        }

        return File::put(app_path('Providers/RepositoryServiceProvider.php'), $this->setContentsProviderFile());
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->hydrator();
        if(!File::exists($this->contractsPath)){
            File::makeDirectory($this->contractsPath, 0755, true);
        }

        if(!File::exists($this->path)){
            File::makeDirectory($this->path, 0755, true);            
        }

        if(!File::exists("$this->contractsPath/AbstractRepositoryInterface.php")){
            File::put("$this->contractsPath/AbstractRepositoryInterface.php", file_get_contents(__DIR__ . './stubs/abstract-repository-interface.stub'));
        }

        if(!File::exists("$this->path/AbstractRepository.php")){
            File::put("$this->path/AbstractRepository.php", str_replace('{{ namespace }}', $this->namespace, file_get_contents(__DIR__ . './stubs/abstract-repository.stub')));
        }

        File::put($this->file, $this->setContents());
        File::put($this->interfaceFile, $this->setContentsInterfaceFile());
        $this->register();

        $this->info('Repository created successfully.');
        return 0;
    }
}
