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
use Behat\Testwork\Hook\Scope\HookScope;

/**
 * Interface for request scopes.
 *
 * @author Pieter Frenssen <pieter@frenssen.be>
 */
interface RequestScope extends HookScope
{
    const BEFORE = 'request.before';
    const AFTER = 'request.after';

    /**
     * Returns the Mink instance.
     *
     * @return Mink
     */
    public function getMink();

    /**
     * Returns the Mink session.
     *
     * @return Session
     */
    public function getSession();

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();
}
