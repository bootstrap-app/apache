<?php

namespace spec\BootstrapApp\Apache;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ApacheFilesGeneratorSpec
 * @package spec\BootstrapApp\Apache
 */
class ApacheFilesGeneratorSpec extends ObjectBehavior
{


    /**
     * @var Laravel illuminate filesystem component
     */
    private $fs;

    /**
     * Setup tests
     */
    function let()
    {
        vfsStream::setup('root_dir', null, [
            'etc' => [
                'apache' => [
                    'conf.d',
                    'conf-available',
                ]
            ]
        ]);

        $this->beConstructedWith(
            "app_name_test",
            "/home/sergi",
            null,
            "24",
            vfsStream::url('root_dir'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('BootstrapApp\Apache\ApacheFilesGenerator');
    }


    function it_create_alias_for_laravel()
    {
        $this->createAliasForLaravel();

        $this->getFilesystem()->get(
            vfsStream::url('root_dir/etc/apache2/conf-available/app_name_test.conf'))->shouldBe(getCompletedStub24());
    }

    function it_create_alias_for_laravel_changing_defaults()
    {
        $this->setAppName("coolapp");
        $this->setBaseDevPath("/home/sergi/apps");
        $this->createAliasForLaravel();

        $this->getFilesystem()->get(
            vfsStream::url('root_dir/etc/apache2/conf-available/coolapp.conf'))->shouldBe(getCompletedStub24ChangingDefaults());
    }

    function it_create_alias_file_for_apache24()
    {
        $this->setApacheVersion("22");
        $this->createAliasForLaravel();
        $this->getFilesystem()->get(
            vfsStream::url('root_dir/etc/apache2/conf.d/app_name_test.conf'))->shouldBe(getCompletedStub22());
    }

}

function getCompletedStub24()
{
    return <<<EOT
Alias /app_name_test /home/sergi/app_name_test/public

<Directory /home/sergi/app_name_test/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
</Directory>


EOT;
}

function getCompletedStub24ChangingDefaults()
{
    return <<<EOT
Alias /coolapp /home/sergi/apps/coolapp/public

<Directory /home/sergi/apps/coolapp/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
</Directory>


EOT;
}

function getCompletedStub22()
{
    return <<<EOT
Alias /app_name_test /home/sergi/app_name_test/public

<Directory /home/sergi/app_name_test/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
</Directory>

EOT;
}

function getStub24()
{
    return <<<EOT
Alias /{{app_name}} {{base_dev_path}}/{{app_name}}/public

<Directory {{base_dev_path}}/{{app_name}}/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
</Directory>


EOT;
}

function getStub22()
{
    return <<<EOT
Alias /{{app_name}} {{base_dev_path}}/{{app_name}}/public

<Directory {{base_dev_path}}/{{app_name}}/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
</Directory>

EOT;
}