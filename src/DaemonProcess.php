<?php
/**
 * Created by Sergi Tur Badenas @2015
 * http://acacha.org/sergitur
 * http://acacha.org
 * Date: 02/03/15
 * Time: 12:36
 */

namespace BootstrapApp\Apache;

use Symfony\Component\Process\Process;


class DaemonProcess
{

    const SERVICE_COMMAND = "/usr/sbin/service";
    const V5_SCRIPTS_FOLDER = "/etc/init.d/";
    const DAEMON_NAME = "skeleton";
    protected $service_command;

    /**
     * @return string
     */
    protected function getDaemonName()
    {
        return $this->daemon_name;
    }

    /**
     * @param string $daemon_name
     */
    protected function setDaemonName($daemon_name)
    {
        $this->daemon_name = $daemon_name;
    }
    /**
     * @var
     */
    protected $scripts_folder;
    protected $daemon_name;
    protected $quiet_mode = true;

    function __construct(
        $service_command = self::SERVICE_COMMAND,
        $scripts_folder = self::V5_SCRIPTS_FOLDER,
        $daemon_name = self::DAEMON_NAME
    ) {
        $this->service_command = $service_command;
        $this->scripts_folder = $scripts_folder;
        $this->daemon_name = $daemon_name;
    }


    /**
     * @return boolean
     */
    public function isQuietMode()
    {
        return $this->quiet_mode;
    }

    /**
     * @param boolean $quiet_mode
     */
    public function setQuietMode($quiet_mode)
    {
        $this->quiet_mode = $quiet_mode;
    }

    public function reload()
    {
        $process = new Process($this->get_reload_command());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        if (!$this->isQuietMode()) {
            echo $process->getOutput();
        }

        return $process->getExitCode();
    }

    protected function get_reload_command()
    {
        if (is_executable($this->service_command)) {
            return $this->service_command . " " . $this->daemon_name . " reload";
        } else {
            if (is_executable($this->scripts_folder)) {
                return $this->scripts_folder . "/" . $this->daemon_name . " reload";
            } else {
                return null;
            }
        }

    }
}