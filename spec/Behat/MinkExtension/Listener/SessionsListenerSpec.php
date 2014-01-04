<?php

namespace spec\Behat\MinkExtension\Listener;

use Behat\Behat\Tester\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Mink\Mink;
use PhpSpec\ObjectBehavior;

class SessionsListenerSpec extends ObjectBehavior
{
    function let(Mink $mink, ScenarioTested $event, FeatureNode $feature, ScenarioNode $scenario)
    {
        $this->beConstructedWith($mink, array('default_session' => 'goutte', 'javascript_session' => 'selenium2'));

        $event->getFeature()->willReturn($feature);
        $event->getScenario()->willReturn($scenario);

        $feature->hasTag('insulated')->willReturn(false);
        $feature->getTags()->willReturn(array());
        $scenario->hasTag('insulated')->willReturn(false);
        $scenario->getTags()->willReturn(array());
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_resets_the_default_session_before_scenarios($event, $mink)
    {
        $mink->resetSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('goutte')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_switches_to_the_javascript_session_for_tagged_scenarios($event, $mink, $scenario)
    {
        $scenario->getTags()->willReturn(array('javascript'));
        $mink->resetSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('selenium2')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_switches_to_the_javascript_session_for_tagged_features($event, $mink, $feature)
    {
        $feature->getTags()->willReturn(array('javascript'));
        $mink->resetSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('selenium2')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_switches_to_a_named_session($event, $mink, $scenario)
    {
        $scenario->getTags()->willReturn(array('mink:test'));
        $mink->resetSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('test')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_prefers_the_scenario_over_the_feature($event, $mink, $scenario, $feature)
    {
        $scenario->getTags()->willReturn(array('mink:test'));
        $feature->getTags()->willReturn(array('javascript'));
        $mink->resetSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('test')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_stops_the_sessions_for_insulated_scenarios($event, $mink, $scenario)
    {
        $scenario->hasTag('insulated')->willReturn(true);
        $mink->stopSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('goutte')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_stops_the_sessions_for_insulated_features($event, $mink, $feature)
    {
        $feature->hasTag('insulated')->willReturn(true);
        $mink->stopSessions()->shouldBeCalled();
        $mink->setDefaultSessionName('goutte')->shouldBeCalled();

        $this->prepareDefaultMinkSession($event);
    }

    function it_stops_the_sessions_at_the_end_of_the_exercise($mink)
    {
        $mink->stopSessions()->shouldBeCalled();

        $this->tearDownMinkSessions();
    }
}
