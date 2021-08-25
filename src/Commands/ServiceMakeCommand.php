<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Service\ServiceCreator;
use Illuminate\Console\Command;

class ServiceMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-service
        {name : The name of the service.}
        {--module= : The application module.}
        {--R|repository= : The repository to be injected into the service.}
        {--r|resource : Generate a resource service class.}
        {--force : Force file creation.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service';

    /**
     * The repository creator instance.
     *
     * @var Eliezer\Chapolim\Services\Service\ServiceCreator
     */
    protected $creator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ServiceCreator $creator)
    {
        parent::__construct();
        $this->creator = $creator;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = trim($this->input->getArgument('name'));
        $module = $this->input->getOption('module'); 
        $repository = $this->input->getOption('repository');
        $resource = $this->input->getOption('resource') ?: false;
        $force = $this->input->getOption('force') ?: false;

        $file = $this->creator->create($name, $module, $repository, $resource, $force);
        $this->line("<info>Service created successfully:</info> {$file}");
    }
}
