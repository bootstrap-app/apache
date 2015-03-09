<?php

namespace BootstrapApp\Apache;

use Illuminate\Filesystem\Filesystem;


/**
 * Class ApacheFilesGenerator
 * @package BootstrapApp\Apache
 */
class ApacheFilesGenerator
{
    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var
     */
    protected $app_name;

    /**
     * @var
     */
    protected $base_dev_path;

    /**
     * @var null
     */
    protected $apache_base_path;

    /**
     * @var string
     */
    protected $apache_version;


    /**
     * @var string
     */
    protected $root;


    /**
     * @param $app_name
     * @param $base_dev_path
     * @param null $apache_base_path
     * @param string $apache_version
     * @param null $root
     */
    public function __construct(
        $app_name,
        $base_dev_path,
        $apache_base_path = null,
        $apache_version = "24",
        $root = null)
    {
        $this->root = $root ?: '/';

        $this->filesystem = new Filesystem();
        $this->app_name = $app_name;
        $this->base_dev_path = $base_dev_path;
        $this->apache_base_path = $apache_base_path;
        $this->apache_version = $apache_version;
    }

    /**
     * @return mixed
     */
    public function getAppName()
    {
        return $this->app_name;
    }

    /**
     * @param mixed $app_name
     */
    public function setAppName($app_name)
    {
        $this->app_name = $app_name;
    }

    /**
     * @return mixed
     */
    public function getBaseDevPath()
    {
        return $this->base_dev_path;
    }

    /**
     * @param mixed $base_dev_path
     */
    public function setBaseDevPath($base_dev_path)
    {
        $this->base_dev_path = $base_dev_path;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return null
     */
    public function getApacheBasePath()
    {
        return $this->apache_base_path;
    }

    /**
     * @param null $apache_base_path
     */
    public function setApacheBasePath($apache_base_path)
    {
        $this->apache_base_path = $apache_base_path;
    }

    /**
     * @return string
     */
    public function getApacheVersion()
    {
        return $this->apache_version;
    }

    /**
     * @param string $apache_version
     */
    public function setApacheVersion($apache_version)
    {
        $this->apache_version = $apache_version;
    }


    /**
     * @param $file
     * @return string
     */
    protected function getPath($file)
    {
        return $this->root . $file;
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    public function getConfFilePath($name)
    {
        ($this->apache_base_path != null) ? $apache_base_path = $this->getPath($this->apache_base_path) :
            $apache_base_path = $this->getPath("/etc/apache2/");

        $suffix = "";
        if ($this->apache_version === "24") {
            $suffix = '/conf-available/' . $name . '.conf';
        } else {
            $suffix = '/conf.d/' . $name . '.conf';
        }

        return $apache_base_path . $suffix;

    }

    /**
     * @param bool $force
     * @return string
     */
    public function createAliasForLaravel($force = true)
    {
        if ($this->filesystem->exists($path = $this->getConfFilePath($this->app_name))) {
            if (!$force) {
                return 'File already exists!';
            }
        }

        $this->makeDirectory($path);
        $this->filesystem->put($path, $this->compileAliasForLaravel());
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->filesystem->isDirectory(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0775, true, true);
        }
    }


    /**
     * @return string
     */
    public function compileAliasForLaravel()
    {
        $stub = $this->getStubFile();

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
     * @param $stub
     * @param $current_value
     * @param $new_value
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
    public function getStubFile()
    {
        $stub_file = $this->apache_version == "24" ?
            '/stubs/apache_24_alias_laravel.stub' : '/stubs/apache_22_alias_laravel.stub';
        return $this->filesystem->get(__DIR__ . $stub_file);
    }
}
