<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Spec\Initializer;

use Behat\Behat\Context\Initializer\ContextInitializer;

use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Funk\Spec;
use Funk\Initializer\SpecInitializer;

class MinkAwareInitializer implements SpecInitializer
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
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeSpec(Spec $spec)
    {
        if (!$context instanceof MinkAwareContext) {
            return;
        }

        $context->setMink($this->mink);
        $context->setMinkParameters($this->parameters);
    }
}
