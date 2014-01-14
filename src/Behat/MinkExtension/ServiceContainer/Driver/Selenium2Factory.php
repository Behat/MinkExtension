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

class Selenium2Factory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'selenium2';
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
                ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
                ->arrayNode('capabilities')
                    ->addDefaultsIfNotSet()
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('browserName')->defaultValue('firefox')->end()
                        ->scalarNode('version')->defaultValue('9')->end()
                        ->scalarNode('platform')->defaultValue('ANY')->end()
                        ->scalarNode('browserVersion')->defaultValue('9')->end()
                        ->scalarNode('browser')->defaultValue('firefox')->end()
                        ->scalarNode('ignoreZoomSetting')->defaultValue('false')->end()
                        ->scalarNode('name')->defaultValue('Behat Test')->end()
                        ->scalarNode('deviceOrientation')->defaultValue('portrait')->end()
                        ->scalarNode('deviceType')->defaultValue('tablet')->end()
                        ->scalarNode('selenium-version')->defaultValue('2.31.0')->end()
                        ->scalarNode('max-duration')->defaultValue('300')->end()
                        ->booleanNode('javascriptEnabled')->end()
                        ->booleanNode('databaseEnabled')->end()
                        ->booleanNode('locationContextEnabled')->end()
                        ->booleanNode('applicationCacheEnabled')->end()
                        ->booleanNode('browserConnectionEnabled')->end()
                        ->booleanNode('webStorageEnabled')->end()
                        ->booleanNode('rotatable')->end()
                        ->booleanNode('acceptSslCerts')->end()
                        ->booleanNode('nativeEvents')->end()
                        ->booleanNode('passed')->end()
                        ->booleanNode('record-video')->end()
                        ->booleanNode('record-screenshots')->end()
                        ->booleanNode('capture-html')->end()
                        ->booleanNode('disable-popup-handler')->end()
                        ->arrayNode('proxy')
                            ->children()
                                ->scalarNode('proxyType')->end()
                                ->scalarNode('proxyAuthconfigUrl')->end()
                                ->scalarNode('ftpProxy')->end()
                                ->scalarNode('httpProxy')->end()
                                ->scalarNode('sslProxy')->end()
                            ->end()
                            ->validate()
                                ->ifTrue(function ($v) {
                                    return empty($v);
                                })
                                ->thenUnset()
                            ->end()
                        ->end()
                        ->arrayNode('firefox')
                            ->children()
                                ->scalarNode('profile')
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return !file_exists($v);
                                        })
                                        ->thenInvalid('Cannot find profile zip file %s')
                                    ->end()
                                ->end()
                                ->scalarNode('binary')->end()
                            ->end()
                        ->end()
                        ->arrayNode('chrome')
                            ->children()
                                ->arrayNode('switches')->prototype('scalar')->end()->end()
                                ->scalarNode('binary')->end()
                                ->arrayNode('extensions')->prototype('scalar')->end()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('wd_host')->defaultValue('http://localhost:4444/wd/hub')->end()
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
                'Install MinkSelenium2Driver in order to use selenium2 driver.'
            );
        }

        return new Definition('Behat\Mink\Driver\Selenium2Driver', array(
            $config['browser'],
            $config['capabilities'],
            $config['wd_host'],
        ));
    }
}
