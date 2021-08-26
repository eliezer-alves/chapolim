<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Repository\{
    RepositoryCreator,
    RepositoryInterfaceCreator,
    RepositoryLayerCreator,
    RepositoryServiceProviderCreator,
};

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepositoryMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-repository
        {name : The name of the repository.}
        {--module= : The application module.}
        {--m|model= : The model to be injected into the repository.}
        {--orm= : folder where the repository will be created - default Eloquent.}
        {--force : Force file creation.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * The repository creator instance.
     *
     * @var Eliezer\Chapolim\Services\Repository\RepositoryCreator
     */
    protected $creator;

    /**
     * The repository creator instance.
     *
     * @var Eliezer\Chapolim\Services\Repository\RepositoryInterfaceCreator
     */
    protected $repositoryInterfaceCreator;

    /**
     * The repository creator instance.
     *
     * @var Eliezer\Chapolim\Services\Repository\RepositoryLayerCreator
     */
    protected $repositoryLayerCreator;

    /**
     * The repository creator instance.
     *
     * @var Eliezer\Chapolim\Services\Repository\RepositoryServiceProviderCreator
     */
    protected $repositoryServiceProviderCreator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RepositoryCreator $creator, RepositoryInterfaceCreator $repositoryInterfaceCreator, RepositoryLayerCreator $repositoryLayerCreator, RepositoryServiceProviderCreator $repositoryServiceProviderCreator)
    {
        parent::__construct();
        $this->creator = $creator;
        $this->repositoryInterfaceCreator = $repositoryInterfaceCreator;
        $this->repositoryLayerCreator = $repositoryLayerCreator;
        $this->repositoryServiceProviderCreator = $repositoryServiceProviderCreator;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = trim($this->input->getArgument('name'));
        $interfaceName = Str::studly($name . 'Interface');
        $module = $this->input->getOption('module'); 
        $model = $this->input->getOption('model');
        $ormFolder = $this->input->getOption('orm');
        $force = $this->input->getOption('force') ?: false;

        if (! File::exists($this->getRepositoryPath($module, $ormFolder) . '/AbstractRepository.php')) {
            $file = $this->repositoryLayerCreator->create($module, $ormFolder, true);
            $this->line("<info>Abstract Repository created successfully:</info> {$file}");
        }

        $file = $this->repositoryInterfaceCreator->create($interfaceName, $module, $force);
        $this->line("<info>Repository Interface created successfully:</info> {$file}");

        $file = $this->creator->create($name, $module, $model, $ormFolder, $force);
        $this->line("<info>Repository created successfully:</info> {$file}");

        if (! File::exists($this->getProviderPath($module) . '/RepositoryServiceProvider.php')) {
            $file = $this->repositoryServiceProviderCreator->create('RepositoryServiceProvider', $module);          
            $this->line("<info>Provider created successfully:</info> {$file}");
        } else {
            $file = $this->repositoryServiceProviderCreator->optimize('RepositoryServiceProvider', $module, $ormFolder);
            $this->line("<info>Provider updated successfully:</info> {$file}");
        }
        
        
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
     * Get path to the class.
     *
     * @param  string  $module
     * @param  string  $ormFolder
     * @return string
     */
    protected function getRepositoryPath($module, $ormFolder)
    {
        $ormFolder = $ormFolder ?? 'Eloquent';
        if (! is_null($module)) {
            return base_path('modules/' . $module . '/Repositories\/' . $ormFolder);
        }

        return app_path('Repositories/' . $ormFolder);
    }
}
