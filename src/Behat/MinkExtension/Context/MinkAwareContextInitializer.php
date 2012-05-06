<?php

namespace Behat\MinkExtension\Context;

use Behat\Behat\Context\ContextInitializerInterface,
    Behat\Behat\Context\ContextInterface;

use Behat\Mink\Mink;

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
class MinkAwareContextInitializer implements ContextInitializerInterface
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
     * Checks if initializer supports provided context.
     *
     * @param ContextInterface $context
     *
     * @return Boolean
     */
    public function supports(ContextInterface $context)
    {
        return $context instanceof MinkAwareContextInterface;
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
}
