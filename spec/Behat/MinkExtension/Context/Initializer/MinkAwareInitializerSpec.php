<?php

namespace spec\Behat\MinkExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use PhpSpec\ObjectBehavior;

class MinkAwareInitializerSpec extends ObjectBehavior
{
    function let(Mink $mink)
    {
        $this->beConstructedWith($mink, array('base_url' => 'foo'));
    }

    function it_is_a_context_initializer()
    {
        $this->shouldHaveType('Behat\Behat\Context\Initializer\ContextInitializer');
    }

    function it_supports_mink_aware_contexts(MinkAwareContext $context)
    {
        $this->supportsContext($context)->shouldBe(true);
    }

    function it_does_not_support_basic_contexts(Context $context)
    {
        $this->supportsContext($context)->shouldBe(false);
    }

    function it_injects_mink_and_parameters_in_mink_aware_contexts(MinkAwareContext $context, $mink)
    {
        $context->setMink($mink)->shouldBeCalled();
        $context->setMinkParameters(array('base_url' => 'foo'))->shouldBeCalled();
        $this->initializeContext($context);
    }
}
