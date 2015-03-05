<?php
/**
 * Created by Sergi Tur Badenas @2015
 * http://acacha.org/sergitur
 * http://acacha.org
 * Date: 04/03/15
 * Time: 09:48
 */

namespace BootstrapApp\Apache\Commands;

use BootstrapApp\Apache\ApacheProcess;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ApacheCommand
 * @package BootstrapApp\Apache\Commands
 */
class ApacheCommand extends Command
{

    /**
     * Configure Symfony console command
     */
    protected function configure()
    {
        $this
            ->setName('apache')
            ->setDescription('create, install an apply Apache files for bootstrap-app')
            ->addArgument(
                'apache:command',
                InputArgument::REQUIRED,
                'The apache command to execute (install | reload | a2enconf )'
            )
            ->addArgument(
                'apache:app_name',
                InputArgument::OPTIONAL,
                'App name'
            )
            ->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path to app'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getArgument('apache:command')) {
            case "install":
                if ($app_name = $input->getArgument('apache:app_name')) {
                    return $this->executeInstall($app_name);
                } else {
                    return $this->executeInstall();
                }
                // no break is possible
            case "reload":
                return $this->executeReload();
            case "a2enconf":
                if ($app_name = $input->getArgument('apache:app_name')) {
                    return $this->executeA2enconf($app_name);
                } else {
                    return $this->executeA2enconf();
                }
                // no break is possible
            default:
                throw new \RuntimeException("Unknown command!");
        }
    }

    /**
     * @return int|null
     */
    private function executeReload()
    {
        $apache = new ApacheProcess();
        return $apache->reload();
    }

    /**
     * Installs app on apache: Create apache config file, enable config (a2enconf) &
     * reload Apache
     *
     * @param null $app_name
     * @return int|null
     */
    private function executeInstall($app_name = null)
    {
        if ($app_name == null) {
            $app_name = $this->getAppName();
        }
        $apache = new ApacheProcess();
        $apache->a2enconf($app_name);
        return $apache->reload();
    }

    /**
     * Execute a2enconf Apache command
     * @param string $app_name
     * @return int|null
     */
    private function executeA2enconf($app_name = "default")
    {
        $apache = new ApacheProcess();
        return $apache->a2enconf($app_name);
    }

    private function jsonDecodeBootstrapApp(){
        $filesystem = new Filesystem();
        $bootstrappAppConfigFile = json_decode($filesystem->get("./bootstrapp-app.json"));
        if ($bootstrappAppConfigFile == null)
            throw new \RuntimeException("Error decoding json file bootstrapp-app.json. Invalid format!");
        else
            return $bootstrappAppConfigFile;
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getAppName()
    {
        if (file_exists("./bootstrapp-app.json")) {
            $filesystem = new Filesystem();
            $bootstrappAppConfigFile = $this->jsonDecodeBootstrapApp();
            if (property_exists($bootstrappAppConfigFile, "name")) {
                return $bootstrappAppConfigFile->name;
            } else {
                throw new \RuntimeException("No name found at file bootstrapp-app.json!");
            }
        } else {
            $app_name = basename(__DIR__);
            return $app_name;
        }
    }
}