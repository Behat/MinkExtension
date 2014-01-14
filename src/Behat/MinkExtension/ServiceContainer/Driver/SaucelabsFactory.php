<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class SaucelabsFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'saucelabs';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('username')->defaultValue(getenv('SAUCE_USERNAME'))->end()
                ->scalarNode('access_key')->defaultValue(getenv('SAUCE_ACCESS_KEY'))->end()
                ->booleanNode('connect')->defaultFalse()->end()
                ->scalarNode('browser')->defaultValue('firefox')->end()
                ->arrayNode('capabilities')
                    ->addDefaultsIfNotSet()
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('name')->defaultValue('Behat feature suite')->end()
                        ->scalarNode('platform')->defaultValue('Linux')->end()
                        ->scalarNode('version')->defaultValue('21')->end()
                        ->scalarNode('selenium-version')->defaultValue('2.31.0')->end()
                        ->scalarNode('max-duration')->defaultValue('300')->end()
                        ->scalarNode('deviceType')->defaultNull()->end()
                        ->scalarNode('deviceOrientation')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(
                'Install MinkSelenium2Driver in order to use saucelabs driver.'
            );
        }
        $capabilities = $config['capabilities'];
        $capabilities['tags'] = array(php_uname('n'), 'PHP '.phpversion());

        if (getenv('TRAVIS_JOB_NUMBER')) {
            $capabilities['tunnel-identifier'] = getenv('TRAVIS_JOB_NUMBER');
            $capabilities['build'] = getenv('TRAVIS_BUILD_NUMBER');
            $capabilities['tags'] = array('Travis-CI', 'PHP '.phpversion());
        }

        $host = 'ondemand.saucelabs.com';
        if ($config['connect']) {
            $host = 'localhost:4445';
        }

        return new Definition('Behat\Mink\Driver\Selenium2Driver', array(
            $config['browser'],
            $capabilities,
            sprintf('%s:%s@%s/wd/hub', $config['username'], $config['access_key'], $host),
        ));
    }
}
