<?php

namespace BootstrapApp\Apache;

use Illuminate\Filesystem\Filesystem;


/**
 * Class ApacheFilesGenerator
 * @package BootstrapApp\Apache
 */
class ApacheFilesGenerator {


    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var
     */
    private $app_name;
    /**
     * @var
     */
    private $base_dev_path;


    /**
     * @param Filesystem $files
     * @param $app_name
     * @param $base_dev_path
     */
    function __construct(Filesystem $files, $app_name, $base_dev_path)
    {
        $this->files = $files;
        $this->app_name = $app_name;
        $this->base_dev_path = $base_dev_path;
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return './apache/' . $name . '.conf';
    }


    /**
     * @param string $apache_version
     * @return string
     */
    public function createAliasForLaravel($apache_version = "24",$force=true)
    {
        if ($this->files->exists($path = $this->getPath($this->app_name)))
        {
            if (!$force) {
                return 'File already exists!';
            }
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->compileAliasForLaravel($apache_version));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if ( ! $this->files->isDirectory(dirname($path)))
        {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }


    /**
     * @param string $apache_version
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function compileAliasForLaravel($apache_version = "24")
    {
        $stub = $this->getStubFile($apache_version);

        $this->replaceAppName($stub, $this->app_name)
            ->replaceBaseDevPath($stub, $this->base_dev_path);

        return $stub;
    }


    /**
     * Replace app_name in stub
     *
     * @param $stub
     * @param $app_name
     * @return $this
     */
    protected function replaceAppName(&$stub, $app_name)
    {
        return $this->replace($stub, '{{app_name}}', $app_name);
    }


    /**
     * @param $stub
     * @param $base_dev_path
     * @return ApacheFilesGenerator
     */
    protected function replaceBaseDevPath(&$stub, $base_dev_path)
    {
        return $this->replace($stub, '{{base_dev_path}}', $base_dev_path);
    }

    /**
     *
     * @param $stub
     * @param $var
     * @return $this
     */
    protected function replace(&$stub, $current_value, $new_value)
    {
        $stub = str_replace($current_value, $new_value, $stub);
        return $this;
    }

    /**
     * @param $apache_version
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getStubFile($apache_version = "24")
    {
        $stub_file = $apache_version == "24" ?
            '/stubs/apache_24_alias_laravel.stub' : '/stubs/apache_22_alias_laravel.stub';
        return $this->files->get(__DIR__ . $stub_file);
    }
}
