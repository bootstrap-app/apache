<?php

namespace spec\BootstrapApp\Apache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Illuminate\Filesystem\Filesystem;

class ApacheFilesGeneratorSpec extends ObjectBehavior {

    function let()
    {
        $this->beConstructedWith(new Filesystem(), "app_name_test", "/home/sergi");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('BootstrapApp\Apache\ApacheFilesGenerator');
    }

    function it_create_alias_file_for_apache22()
    {
        $this->createAliasForLaravel("22")->shouldBe(null);
    }

    function it_create_alias_file_for_apache24()
    {
        $this->createAliasForLaravel()->shouldBe(null);
    }

    function it_create_alias_file_for_apache22_null_aready_exists()
    {
        $this->createAliasForLaravel("22", false)->shouldBe("File already exists!");
    }

    function it_create_alias_file_for_apache24_null_aready_exists()
    {
        $this->createAliasForLaravel("24",false)->shouldBe("File already exists!");
    }

    function it_compile_alias_for_laravel()
    {
        $this->compileAliasForLaravel()->shouldBe(getCompletedStub24());
    }

    function it_compile_alias_for_laravel_22()
    {
        $this->compileAliasForLaravel("22")->shouldBe(getCompletedStub22());
    }

    function it_get_stub_file() {

        $this->getStubFile()->shouldBe(getStub24());
    }

    function it_get_stub_file22() {

        $this->getStubFile("22")->shouldBe(getStub22());
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