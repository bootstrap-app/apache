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


class ApacheProcess extends DaemonProcess
{
    const APACHE_22_CONFIG_DIR = "/etc/apache2/conf.d/";

    const APACHE_24_CONFIG_AVAILABLE_DIR = "/etc/apache2/conf-available/";

    const APACHE_24_CONFIG_ENABLE_DIR = "/etc/apache2/conf-enabled/";

    const A2ENCONF_EXECUTABLE = "/usr/sbin/a2enconf";
    /**
     * @var string
     */
    private $a2enconf_executable;

    function __construct($a2enconf_executable = self::A2ENCONF_EXECUTABLE)
    {
        parent::__construct();
        $this->setDaemonName("apache2");
        $this->a2enconf_executable = $a2enconf_executable;
    }


    protected function get_enable_config_command($config_file_name){
        if ( is_executable($this->a2enconf_executable)) {
            return $this->a2enconf_executable . " " . $config_file_name;
        }
    }

    public function a2enconf($config_file_name){
        return $this->run_command($this->get_enable_config_command($config_file_name));
    }

}