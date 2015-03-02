<?php

namespace spec\BootstrapApp\Apache;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use BootstrapApp\Apache\ApacheFilesGenerator;
use Illuminate\Filesystem\Filesystem;


class ApacheProcessSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('BootstrapApp\Apache\ApacheProcess');
    }

    function it_reloads(){
        $this->reload()->shouldBe(0);
    }

    function it_apply_config_and_reloads(){

        $filegenerator = new ApacheFilesGenerator(new Filesystem(), "app_name_test", "/home/sergi");

        $filegenerator->createAliasForLaravel();
        $this->a2enconf("app_name_test")->shouldBe(0);
        $this->reload()->shouldBe(0);
    }


}
