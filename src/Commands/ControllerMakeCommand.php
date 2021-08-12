<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Controller\ControllerCreator;
use Illuminate\Console\Command;

class ControllerMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-controller
        {name : The name of the controller.}
        {--module= : The application module.}
        {--S|service= : The service to be injected into the controller.}
        {--route : Generate controller api routes.}
        {--r|resource : Generate a resource controller class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller';

    /**
     * The controller creator instance.
     *
     * @var Eliezer\Chapolim\Services\Controller\ControllerCreator
     */
    protected $creator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ControllerCreator $creator)
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
        $service = $this->input->getOption('service');
        $resource = $this->input->getOption('resource') ?: false;

        $file = $this->creator->create($name, $module, $service, $resource);
        $this->line("<info>Controller created successfully:</info> {$file}");
    }
}
