<?php

namespace Eliezer\Chapolim\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Creator
{
    protected $path;
    protected $name;
    protected $module;
    protected $namespace;
    protected $fillable;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Make sure the class does not already exist.
     *
     * @param  string  $name
     * @param  string  $path
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureClassDoesntAlreadyExist($name, $path = null)
    {
        if (! empty($path)) {
            $files = $this->files->glob($path.'/*.php');

            foreach ($files as $file) {
                $this->files->requireOnce($file);
                if(basename($file, '.php') == ($className = $this->getClassName($name))){
                    throw new InvalidArgumentException("A {$className} class already exists.");        
                }
            }
        }
    }

    /**
     * Get the class name of a class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the class.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$name.'.php';
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

}