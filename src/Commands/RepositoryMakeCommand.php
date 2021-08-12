<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Repository\RepositoryCreator;
use Eliezer\Chapolim\Services\Repository\RepositoryInterfaceCreator;
use Illuminate\Console\Command;

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
        {--orm= : folder where the repository will be created - default Eloquent.}';

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RepositoryCreator $creator, RepositoryInterfaceCreator $repositoryInterfaceCreator)
    {
        parent::__construct();
        $this->creator = $creator;
        $this->repositoryInterfaceCreator = $repositoryInterfaceCreator;
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

        $file = $this->repositoryInterfaceCreator->create($interfaceName, $module);
        $this->line("<info>Repository Interface created successfully:</info> {$file}");

        $file = $this->creator->create($name, $module, $model, $ormFolder);
        $this->line("<info>Repository created successfully:</info> {$file}");
    }
}
