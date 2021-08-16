<?php

namespace Eliezer\Chapolim\Services\Controller;

use Eliezer\Chapolim\Services\Creator;
use Illuminate\Support\Str;

class ApiRouteGroupCreator extends Creator
{

    public function create($name, $module = null)
    {        
        $path = $this->getPath('api', $this->getRoutePath($module));
        $routeFile = $this->files->get($path);
        $stub = $this->getStub();

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->updateRouteFile($name, $module, $stub, $routeFile)
        );

        return $path;
    }

    /**
     * Get the controller stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->stubPath().'/route-group.stub';

        return $this->files->get($stub);
    }

    /**
     * Update route file with new route group.
     *
     * @param  string  $name
     * @param  string  $module
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function updateRouteFile($name, $module, $stub, $routeFile)
    {
        $prefix = strtolower(preg_replace(["/([A-Z]+)/", "/ ([A-Z]+)([A-Z][a-z])/"], ["-$1", "_$1_$2"], lcfirst($name)));
        $routeDescription = preg_replace(["/([A-Z]+)/", "/ ([A-Z]+)([A-Z][a-z])/"], [" $1", "_$1_$2"], $name);
        $useClass = 'use ' . $this->getNamespace($module) . '\\' . $this->getClassName($name) . ';';

        $stub = str_replace(
            ['DummyRouteDescription', '{{ routeDescription }}', '{{routeDescription}}'],
            $routeDescription, $stub
        );

        $stub = str_replace(
            ['DummyPrefix', '{{ prefix }}', '{{prefix}}'],
            $prefix, $stub
        );

        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );

        $routeFile = str_replace(
            '<?php', "<?php\n\n$useClass", str_replace(
                $useClass, '', str_replace(
                    $stub, '', $routeFile
                )
            )
        ) . $stub;

        return $routeFile;
    }

    /**
     * Get the class namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($module)
    {
        return is_null($module) ? 'App\Http\Controllers' : 'Modules\\' . Str::studly($module) . '\Http\Controllers';
    }

    /**
     * Get path to the route file.
     *
     * @param  string  $module
     * @return string
     */
    protected function getRoutePath($module)
    {
        if (! is_null($module)) {
            return base_path('modules/' . $module . '/Routes');
        }

        return base_path('routes');
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