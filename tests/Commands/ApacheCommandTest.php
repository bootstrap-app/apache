<?php
/**
 * Created by Sergi Tur Badenas @2015
 * http://acacha.org/sergitur
 * http://acacha.org
 * Date: 04/03/15
 * Time: 10:32
 */

namespace BootstrapApp\tests;

use BootstrapApp\Apache\Commands\ApacheCommand;
use League\Flysystem\File;
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
     * @var
     */
    private $root;

    /**
     *
     */
    /*
    protected function setUp()
    {
        parent::setUp();

        $this->setupFakeApacheFileSystem();

    }*/

    /**
     *
     */
    /*
    protected function setupFakeApacheFileSystem() {

        $structure = array(
            'etc' => array(
                'apache' => array(
                    'conf-available' => array(
                    ),
                    'conf.d' => array(
                    )
                ),
            )
        );
        $this->root = vfsStream::create($structure);

    } */

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
        $this->setExpectedException('RuntimeException', 'Unknown command!');
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
            $this->setExpectedException('RuntimeException', 'Command has to be executed by root');
        } else {
            $this->setExpectedException('RuntimeException', 'ERROR: Conf default does not exist!');
        }

        $commandTester = $this->executeApacheCommand("a2enconf");

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());

    }

    /**
     *
     */
    protected function unlinkBootstrappAppJson()
    {
        unlink("./bootstrapp-app.json");
    }

    /**
     * Test execution of install command with and app_name and base developer path
     */
    public
    function testExecuteInstallCommand()
    {
        $this->executeCommand("install", "app_name1", "/usr/share");
    }

    /**
     * Test execution of apache a2enconf
     */
    public function testExecuteA2enconf()
    {
        $this->executeCommand("a2enconf", "app_name", "/usr/share");
    }

    /**
     * @param $command
     * @param $app_name
     * @param $path
     */
    public function executeCommand($command, $app_name, $path)
    {
        //Expected RunTimeException is test is no executed as root
        if (!(0 == posix_getuid())) {
            $this->setExpectedException('RuntimeException', 'Command has to be executed by root');
        }

        $fs = new Filesystem();
        $fg = new ApacheFilesGenerator($fs, $app_name, $path);

        $fg->createAliasForLaravel();
        $commandTester = $this->executeApacheCommand($command, $app_name);

        //No news Good news!
        $this->assertEquals("", $commandTester->getDisplay());
    }

    /**
     * Helper to execute commands
     * @return CommandTester
     */
    protected
    function executeApacheCommand($command, $app_name = null)
    {
        $input = array('apache:command' => $command);
        if ($app_name != null) {
            $input['apache:app_name'] = $app_name;
        }
        return $this->executeApache($input);
    }

    /**
     * @return CommandTester
     */
    protected
    function executeApacheReload()
    {
        return $this->executeApache(array(
            'apache:command' => 'reload',
        ));
    }


    /**
     * @param array $input
     * @return CommandTester
     */
    protected
    function executeApache(array $input)
    {
        $application = new Application();
        $application->add(new ApacheCommand());

        $command = $application->find('apache');
        $commandTester = new CommandTester($command);

        $commandTester->execute($input);
        return $commandTester;
    }

    /**
     * @depends testExecuteA2enconf
     * @depends testExecuteReload
     */
    public function testExecuteInstall()
    {

    }


}