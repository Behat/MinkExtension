<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;

class PantherFactorySpec extends ObjectBehavior
{
    function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    function it_is_named_goutte()
    {
        $this->getDriverName()->shouldReturn('panther');
    }

    function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(true);
    }
}
