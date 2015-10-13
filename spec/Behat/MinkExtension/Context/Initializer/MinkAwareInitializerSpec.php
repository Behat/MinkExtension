<?php

namespace spec\Behat\MinkExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Testwork\Call\CallCenter;
use Behat\Testwork\Environment\EnvironmentManager;
use Behat\Testwork\Hook\HookDispatcher;
use Behat\Testwork\Hook\HookRepository;
use PhpSpec\ObjectBehavior;

class MinkAwareInitializerSpec extends ObjectBehavior
{
    function let(Mink $mink)
    {
        // HookDispatcher() is a final class so it cannot be reflected into a
        // mocked instance. Let's instantiate a real object.
        $dispatcher = new HookDispatcher(new HookRepository(new EnvironmentManager()), new CallCenter());

        $this->beConstructedWith($mink, array('base_url' => 'foo'), $dispatcher);
    }

    function it_is_a_context_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_does_nothing_for_basic_contexts(Context $context)
    {
        $this->initializeContext($context);
    }

    function it_injects_mink_and_parameters_in_mink_aware_contexts(MinkAwareContext $context, $mink)
    {
        // HookDispatcher() is a final class so it cannot be reflected into a
        // mocked instance. Let's instantiate a real object.
        $dispatcher = new HookDispatcher(new HookRepository(new EnvironmentManager()), new CallCenter());

        $context->setMink($mink)->shouldBeCalled();
        $context->setMinkParameters(array('base_url' => 'foo'))->shouldBeCalled();
        $context->setDispatcher($dispatcher)->shouldBeCalled();
        $this->initializeContext($context);
    }
}
