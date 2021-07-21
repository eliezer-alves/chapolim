<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChapolimModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapolim:model {name} {--F|fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model class with service layer';

    protected $file;
    protected $path;
    protected $name;
    protected $namespace;
    protected $fillable;
    protected $textFillable;
    protected $table;

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
        $this->namespace = 'App\Models';
        $this->path = app_path("Models");
        $this->file = "$this->path/$this->name.php";
        $this->table = strtolower(preg_replace(["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"], ["_$1", "_$1_$2"], lcfirst($this->name)));
        $this->defineFillable();
    }

    /**
     * Returns fillable array from fillable option
     *
     * @return void
     */
    private function defineFillable() : void
    {
        if(!$this->option('fillable')) return;

        $columns = explode('|', $this->option('fillable'));
        $this->fillable = [];
        array_map(function ($column){
            $column = explode(',', $column);

            $this->textFillable .= "\n\t\t\t'$column[0]',";

            array_push($this->fillable, [
                'column' => $column[0],
                'type' => $column[1] ?? NULL,
            ]);
        }, $columns);
    }

    /**
     * Returns the contents of the file to be created.
     *
     * @return void
     */
    private function setContents()
    {
        if($this->fillable){
            $template = file_get_contents(__DIR__ . './stubs/model.fillable.stub');
        }else{
            $template = file_get_contents(__DIR__ . './stubs/model.stub');
        }

        return str_replace('{{ namespace }}', $this->namespace,
            str_replace('{{ class }}', $this->name,
            str_replace('{{ table }}', $this->table,
            str_replace('{{ fillableColumns }}', $this->textFillable, $template)
        )));
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

        $this->info('Model created successfully.');
        return 0;
    }
}
