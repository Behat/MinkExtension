<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context\ClassGuesser;

use Behat\Behat\Context\ClassGuesser\ClassGuesserInterface;

/**
 * Mink context class guesser.
 * Provides Mink context class if no other class found.
 */
class MinkContextClassGuesser implements ClassGuesserInterface
{
    /**
     * Tries to guess context classname.
     *
     * @return string
     */
    public function guess()
    {
        return 'Behat\\MinkExtension\\Context\\MinkContext';
    }
}
