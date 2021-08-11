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
        {--module= : The model module.}
        {--F|fillable= : The fillable attribute of the model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model';

    /**
     * The migration creator instance.
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

        $file = $this->creator->create($name, $module, $fillable);
        $this->line("<info>Model created successfully:</info> {$file}");
    }
}
