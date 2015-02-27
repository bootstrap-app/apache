<?php

namespace spec\BootstrapApp\Apache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Illuminate\Filesystem\Filesystem;

class ApacheFilesGeneratorSpec extends ObjectBehavior {

    function let(Filesystem $files)
    {

        $this->beConstructedWith($files, "app_name_test", "/home/sergi");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('BootstrapApp\Apache\ApacheFilesGenerator');
    }

    function it_create_alias_file_for_apache22()
    {
        $this->createAliasForLaravel();
    }
}

function getStub()
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