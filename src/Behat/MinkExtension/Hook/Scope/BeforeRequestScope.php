<?php
/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Hook\Scope;

/**
 * Represents an BeforeRequest hook scope.
 *
 * @author Pieter Frenssen <pieter@frenssen.be>
 */
final class BeforeRequestScope extends BaseRequestScope
{

    /**
     * Returns hook scope name.
     *
     * @return string
     */
    public function getName()
    {
        return self::BEFORE;
    }

}

