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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ApacheCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('apache')
            ->setDescription('create, install an apply Apache files for bootstrap-app')
            ->addArgument(
                'apache:command',
                InputArgument::REQUIRED,
                'The apache command to execute'
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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument('apache:command');

        switch ($command) {
            case "install":

                if ($app_name = $input->getArgument('apache:app_name')) {
                    return $this->executeInstall($app_name);
                } else {
                    return $this->executeInstall();
                }
                break;
            case "reload":
                return $this->executeReload();
                break;
            case "a2enconf":
                if ($app_name = $input->getArgument('apache:app_name')) {
                    return $this->executeA2enconf($app_name);
                } else {
                    return $this->executeA2enconf();
                }

                break;
            default:
                throw new \RuntimeException("unkown command!");
        }


    }

    private function executeReload()
    {
        $apache = new ApacheProcess();
        return $apache->reload();
    }

    private function executeInstall($app_name = "default")
    {
        $apache = new ApacheProcess();
        $apache->a2enconf($app_name);
        return $apache->reload();
    }

    private function executeA2enconf($app_name = "default")
    {
        $apache = new ApacheProcess();
        return $apache->a2enconf($app_name);
    }
}