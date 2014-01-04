<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Listener;

use Behat\Behat\Tester\Event\ScenarioTested;
use Behat\Mink\Mink;
use Behat\Testwork\Tester\Event\ExerciseCompleted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Mink sessions listener.
 * Listens Behat events and configures/stops Mink sessions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SessionsListener implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::BEFORE   => array('prepareDefaultMinkSession', 10),
            ExerciseCompleted::AFTER => array('tearDownMinkSessions', -10)
        );
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
     * @param ScenarioTested $event
     */
    public function prepareDefaultMinkSession(ScenarioTested $event)
    {
        $scenario = $event->getScenario();
        $feature  = $event->getFeature();
        $session  = $this->parameters['default_session'];

        foreach (array_merge($feature->getTags(), $scenario->getTags()) as $tag) {
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
