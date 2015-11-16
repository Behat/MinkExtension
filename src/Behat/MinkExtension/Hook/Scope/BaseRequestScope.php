<?php
/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Hook\Scope;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\Hook\Scope\HookScope;
use Behat\Testwork\Suite\GenericSuite;

/**
 * Base class for request scopes.
 *
 * @author Pieter Frenssen <pieter@frenssen.be>
 */
abstract class BaseRequestScope implements RequestScope
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * Initializes the scope.
     */
    public function __construct(Mink $mink, Session $session, $path)
    {
        $this->mink = $mink;
        $this->session = $session;
        $this->path = $path;

        // @todo: Figure out how to pass in the actual Environment.
        $this->environment = new StaticEnvironment(new GenericSuite('suite', []));
    }

    /**
     * Returns the Mink instance.
     *
     * @return Mink
     */
    public function getMink()
    {
        return $this->mink;
    }

    /**
     * Returns the Mink session.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns hook suite.
     *
     * @return Suite
     */
    public function getSuite()
    {
        return $this->environment->getSuite();
    }

    /**
     * Returns hook environment.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
