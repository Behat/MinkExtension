<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;

use Behat\Testwork\Hook\HookDispatcher;

/**
 * Mink aware contexts initializer.
 * Sets Mink instance and parameters to the MinkAware contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkAwareInitializer implements ContextInitializer
{
    private $mink;
    private $parameters;
    private $dispatcher;

    /**
     * Initializes initializer.
     *
     * @param Mink           $mink
     * @param array          $parameters
     * @param HookDispatcher $dispatcher
     */
    public function __construct(Mink $mink, array $parameters, HookDispatcher $dispatcher)
    {
        $this->mink       = $mink;
        $this->parameters = $parameters;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof MinkAwareContext) {
            return;
        }

        $context->setMink($this->mink);
        $context->setMinkParameters($this->parameters);
        $context->setDispatcher($this->dispatcher);
    }
}
