<?php

namespace spec\Behat\MinkExtension\ServiceContainer\Driver;

use PhpSpec\ObjectBehavior;

class Symfony2FactorySpec extends ObjectBehavior
{
    function it_is_a_driver_factory()
    {
        $this->shouldHaveType('Behat\MinkExtension\ServiceContainer\Driver\DriverFactory');
    }

    function it_is_named_symfony2()
    {
        $this->getDriverName()->shouldReturn('symfony2');
    }

    function it_does_not_support_javascript()
    {
        $this->supportsJavascript()->shouldBe(false);
    }
}
