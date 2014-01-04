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
use Behat\MinkExtension\Context\MinkAwareInterface;

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
     * @param Context $context
     *
     * @return Boolean
     */
    public function supportsContext(Context $context)
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
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        $context->setMink($this->mink);
        $context->setMinkParameters($this->parameters);
    }
}
