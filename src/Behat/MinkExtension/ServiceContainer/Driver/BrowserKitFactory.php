<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class BrowserKitFactory implements DriverFactory
{
    public function getDriverName(): string
    {
        return 'browserkit';
    }

    public function supportsJavascript(): bool
    {
        return false;
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
    }

    public function buildDriver(array $config): Definition
    {
        if (!class_exists(BrowserKitDriver::class)) {
            throw new \RuntimeException('Install BrowserKitDriver (from behat/mink-browserkit-driver) in order to use symfony/browser-kit');
        }

        if (!class_exists(HttpBrowser::class)) {
            throw new \RuntimeException('Install symfony/browser-kit to use the browserkit driver');
        }

        if (!class_exists(HttpClient::class)) {
            throw new \RuntimeException('Install symfony/http-client to use the browserkit driver');
        }

        return new Definition(BrowserKitDriver::class, [new Definition(HttpBrowser::class)]);
    }
}
