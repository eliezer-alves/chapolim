<?php

namespace Eliezer\Chapolim\Services\Model;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class ModelCreator extends Creator
{

    public function create($name, $module = null, $fillable = null)
    {
        $this->ensureClassDoesntAlreadyExist($name, $this->getModelPath($module));
        
        $fillable = $this->makeFillable($fillable);
        $stub = $this->getStub($fillable);
        $path = $this->getPath($name, $this->getModelPath($module));

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $module, $stub, $fillable)
        );

        return $path;
    }

    /**
     * Get the model stub file.
     *
     * @param  string|null  $fillable
     * @param  bool  $create
     * @return string
     */
    protected function getStub($fillable)
    {
        if (is_null($fillable)) {
            $stub = $this->stubPath().'/model.stub';
        } else {
            $stub = $this->stubPath().'/model.fillable.stub';
        }

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the model stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $module, $stub, $fillable)
    {
        $stub = str_replace(
            ['DummyNamespace', '{{ namespace }}', '{{namespace}}'],
            $this->getNamespace($module), $stub
        );

        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );
        
        $stub = str_replace(
            ['Dummytable', '{{ table }}', '{{table}}'],
            $this->getTable($name), $stub
        );

        if (! is_null($fillable)) {
            $stub = str_replace(
                ['DummyFillable', '{{ fillable }}', '{{fillable}}'],
                $fillable, $stub
            );
        }

        return $stub;
    }

    /**
     * Get the class name of a class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module) ? 'App\Models' : 'Modules\\' . Str::studly($module) . '\Models';
    }
    /**
     * Get the table name of a model.
     *
     * @param  string  $name
     * @return string
     */
    protected function getTable($name)
    {
        return Str::snake($name);
    }

    /**
     * Get path to the model.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getModelPath($module)
    {
        if(! is_null($module)) {
            return base_path('modules/Models');
        }

        return app_path('Models');
    }
    
    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
    
    /**
     * Get the path to the stubs.
     * 
     * @param  string  $fillable
     * @return string
     */
    public function makeFillable($fillable)
    {
        if(is_null($fillable)) return;

        $columnsFillable = '';

        foreach (explode('|', $fillable) as $column) {
            $columnsFillable .= "\n\t    '$column',";
        }
        
        return substr($columnsFillable, 1);
    }

}