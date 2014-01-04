<?php

namespace spec\Behat\MinkExtension;

use PhpSpec\ObjectBehavior;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_named_mink()
    {
        $this->getConfigKey()->shouldReturn('mink');
    }
}
