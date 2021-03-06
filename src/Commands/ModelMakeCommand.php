<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Model\ModelCreator;
use Illuminate\Console\Command;

class ModelMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-model
        {name : The name of the model.}
        {--module= : The application module.}
        {--F|fillable= : The fillable attribute of the model.}
        {--force : Force file creation.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class with table attribute and fillable attribute';

    /**
     * The model creator instance.
     *
     * @var Eliezer\Chapolim\Services\Model\ModelCreator
     */
    protected $creator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ModelCreator $creator)
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
        $fillable = $this->input->getOption('fillable');
        $force = $this->input->getOption('force') ?: false;

        $file = $this->creator->create($name, $module, $fillable, $force);
        $this->line("<info>Model created successfully:</info> {$file}");
    }
}
