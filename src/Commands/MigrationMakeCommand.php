<?php

namespace Eliezer\Chapolim\Commands;

use Eliezer\Chapolim\Services\Migration\MigrationCreator;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Support\Str;

class MigrationMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:make-migration
        {name : The name of the migration.}
        {--module= : The migration module.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}
        {--columns= : Table columns to migrate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration';

    /**
     * The migration creator instance.
     *
     * @var Eliezer\Chapolim\Services\Migration\MigrationCreator
     */
    protected $creator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MigrationCreator $creator)
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
        $name = Str::snake(trim($this->input->getArgument('name')));
        $module = $this->input->getOption('module'); 
        $table = $this->input->getOption('table');
        $create = $this->input->getOption('create') ?: false;
        
        // If no table was given as an option but a create option is given then we
        // will use the "create" option as the table name. This allows the devs
        // to pass a table name into this option as a short-cut for creating.
        if (! $table && is_string($create)) {
            $table = $create;

            $create = true;
        }

        // Next, we will attempt to guess the table name if this the migration has
        // "create" in the name. This will allow us to provide a convenient way
        // of creating migrations that create new tables for the application.
        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        $file = $this->creator->create($name, $module, $table, $create);
        $this->line("<info>Created Migration:</info> {$file}");
    }
}
