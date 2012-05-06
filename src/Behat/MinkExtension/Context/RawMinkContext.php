<?php

namespace Behat\MinkExtension\Context;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent;

use Behat\Mink\Mink,
    Behat\Mink\WebAssert;

/*
 * This file is part of the Behat\MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Raw Mink context for Behat BDD tool.
 * Provides raw Mink integration (without step definitions) and web assertions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class RawMinkContext extends BehatContext implements MinkAwareContextInterface
{
    private $mink;
    protected $minkParameters;

    /**
     * Sets Mink instance.
     *
     * @param Mink $mink Mink session manager
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * Sets parameters provided for Mink.
     *
     * @param array $parameters
     */
    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    /**
     * Returns Mink instance.
     *
     * @return Mink
     */
    public function getMink()
    {
        return $this->mink;
    }

    /**
     * Returns Mink session.
     *
     * @param string|null $name name of the session OR active session will be used
     *
     * @return Session
     */
    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * Returns Mink session assertion tool.
     *
     * @param string|null $name name of the session OR active session will be used
     *
     * @return WebAssert
     */
    public function assertSession($name = null)
    {
        return $this->getMink()->assertSession($name);
    }

    /**
     * @BeforeScenario
     */
    public function prepareMinkSessions($event)
    {
        $scenario = $event instanceof ScenarioEvent ? $event->getScenario() : $event->getOutline();
        $session  = $this->minkParameters['default_session'];

        foreach ($scenario->getTags() as $tag) {
            if ('javascript' === $tag) {
                $session = $this->minkParameters['javascript_session'];
            } elseif (preg_match('/^mink\:(.+)/', $tag, $matches)) {
                $session = $matches[1];
            }
        }

        if ($scenario->hasTag('insulated')) {
            $this->getMink()->stopSessions();
        } else {
            $this->getMink()->resetSessions();
        }

        $this->getMink()->setDefaultSessionName($session);
    }
}
