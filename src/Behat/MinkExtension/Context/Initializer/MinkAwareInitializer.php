<?php

namespace Behat\MinkExtension\Context\Initializer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Event\OutlineEvent;

use Behat\Mink\Mink;

use Behat\MinkExtension\Context\MinkAwareInterface;

/*
 * This file is part of the Behat\MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mink aware contexts initializer.
 * Sets Mink instance and parameters to the MinkAware contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkAwareInitializer implements InitializerInterface, EventSubscriberInterface
{
    private $mink;
    private $parameters;

    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(Mink $mink, array $parameters)
    {
        $this->mink       = $mink;
        $this->parameters = $parameters;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'beforeScenario' => 'prepareDefaultMinkSession',
            'beforeOutline'  => 'prepareDefaultMinkSession',
            'afterSuite'     => 'tearDownMinkSessions'
        );
    }

    /**
     * Checks if initializer supports provided context.
     *
     * @param ContextInterface $context
     *
     * @return Boolean
     */
    public function supports(ContextInterface $context)
    {
        // if context/subcontext implements MinkAwareInterface
        if ($context instanceof MinkAwareInterface) {
            return true;
        }

        // if context/subcontext uses MinkDictionary trait
        $refl = new \ReflectionObject($context);
        if (method_exists($refl, 'getTraitNames')) {
            if (in_array('Behat\\MinkExtension\\Context\\MinkDictionary', $refl->getTraitNames())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setMink($this->mink);
        $context->setMinkParameters($this->parameters);
    }

    /**
     * Configures default Mink session before each scenario.
     * Configuration is based on provided scenario tags:
     *
     * `@javascript` tagged scenarios will get `javascript_session` as default session
     * `@mink:CUSTOM_NAME tagged scenarios will get `CUSTOM_NAME` as default session
     * Other scenarios get `default_session` as default session
     *
     * `@insulated` tag will cause Mink to stop current sessions before scenario
     * instead of just soft-resetting them
     *
     * @param ScenarioEvent|OutlineEvent $event
     */
    public function prepareDefaultMinkSession($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $session  = $this->parameters['default_session'];

        foreach ($scenario->getTags() as $tag) {
            if ('javascript' === $tag) {
                $session = $this->parameters['javascript_session'];
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        if ($scenario->hasTag('insulated')) {
            $this->mink->stopSessions();
        } else {
            $this->mink->resetSessions();
        }

        $this->mink->setDefaultSessionName($session);
    }

    /**
     * Stops all started Mink sessions.
     */
    public function tearDownMinkSessions()
    {
        $this->mink->stopSessions();
    }
}
