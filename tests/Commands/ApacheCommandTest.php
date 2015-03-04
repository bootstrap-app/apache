<?php
/**
 * Created by Sergi Tur Badenas @2015
 * http://acacha.org/sergitur
 * http://acacha.org
 * Date: 04/03/15
 * Time: 10:32
 */

use BootstrapApp\Apache\Commands\ApacheCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use BootstrapApp\Apache\ApacheFilesGenerator;
use Illuminate\Filesystem\Filesystem;


/**
 * Class ApacheCommandTest
 */
class ApacheCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Execute test for Apache command
     */
    public function testExecuteReload()
    {
        //Expected RunTimeException is test is no executed as root
        if (!(0 == posix_getuid())) {
            $this->setExpectedException('RuntimeException', 'Command has to be executed by root');
        }

        $commandTester = $this->executeApacheReload();

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());

    }

    /**
     * Execute test for Apache a2enconf command
     */
    public function testExecuteUnknownCommand()
    {
        $this->setExpectedException('RuntimeException', 'unkown command!');
        $this->executeApache(array(
            'apache:command' => 'asdasdasdasdasd',
        ));
    }

    /**
     * Execute test for Apache a2enconf command
     *
     */
    public function testExecuteA2enconfNotConfExists()
    {
        //Expected RunTimeException is test is no executed as root
        if (!(0 == posix_getuid())) {
            $this->setExpectedException('RuntimeException','Command has to be executed by root');
        } else {
            $this->setExpectedException('RuntimeException','ERROR: Conf default does not exist!');
        }

        $commandTester = $this->executeApacheA2enconf();

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());

    }

    /**
     *
     */
    public function testExecuteA2enconf()
    {
        //Expected RunTimeException is test is no executed as root
        if (!(0 == posix_getuid())) {
            $this->setExpectedException('RuntimeException','Command has to be executed by root');
        }

        $fg = new ApacheFilesGenerator( new Filesystem(),"app_name","/usr/share/app_name");

        $fg->createAliasForLaravel();
        $commandTester = $this->executeApacheA2enconf("app_name");

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());

    }


    /**
     * @depends testExecuteA2enconf
     * @depends testExecuteReload
     */
    public function testExecuteInstall()
    {

    }

    public function testExecuteInstall2()
    {
        //Expected RunTimeException is test is no executed as root
        if (!(0 == posix_getuid())) {
            $this->setExpectedException('RuntimeException','Command has to be executed by root');
        }

        $fg = new ApacheFilesGenerator( new Filesystem(),"app_name1","/usr/share/app_name1");

        $fg->createAliasForLaravel();
        $commandTester = $this->executeApacheInstall("app_name1");

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());

    }

    /**
     * @return CommandTester
     */
    protected function executeApacheInstall($app_name=null)
    {
        $input = array ('apache:command' => 'install');
        if ($app_name != null) {
            $input['apache:app_name'] = $app_name;
        }
        return $this->executeApache($input);
    }

    /**
     * @return CommandTester
     */
    protected function executeApacheReload()
    {
        return $this->executeApache(array(
            'apache:command' => 'reload',
        ));
    }

    /**
     * @param null $app_name
     * @return CommandTester
     */
    protected function executeApacheA2enconf($app_name=null)
    {
        $input = array ('apache:command' => 'a2enconf');
        if ($app_name != null) {
            $input['apache:app_name'] = $app_name;
        }
        return $this->executeApache($input);
    }

    /**
     * @param array $input
     * @return CommandTester
     */
    protected function executeApache(array $input)
    {
        $application = new Application();
        $application->add(new ApacheCommand());

        $command = $application->find('apache');
        $commandTester = new CommandTester($command);

        $commandTester->execute($input);
        return $commandTester;
    }


}