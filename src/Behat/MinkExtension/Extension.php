<?php

namespace Behat\MinkExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Mink extension for Behat class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Extension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');

        if (isset($config['mink_loader'])) {
            $basePath = $container->getParameter('behat.paths.base');

            if (file_exists($basePath.DIRECTORY_SEPARATOR.$config['mink_loader'])) {
                require($basePath.DIRECTORY_SEPARATOR.$config['mink_loader']);
            } else {
                require($config['mink_loader']);
            }
        }

        if (isset($config['goutte'])) {
            if (!class_exists('Behat\\Mink\\Driver\\GoutteDriver')) {
                throw new \RuntimeException(
                    'Install MinkGoutteDriver in order to activate goutte session.'
                );
            }

            $loader->load('sessions/goutte.xml');
        }
        if (isset($config['sahi'])) {
            if (!class_exists('Behat\\Mink\\Driver\\SahiDriver')) {
                throw new \RuntimeException(
                    'Install MinkSahiDriver in order to activate sahi session.'
                );
            }

            $loader->load('sessions/sahi.xml');
        }
        if (isset($config['zombie'])) {
            if (!class_exists('Behat\\Mink\\Driver\\ZombieDriver')) {
                throw new \RuntimeException(
                    'Install MinkZombieDriver in order to activate zombie session.'
                );
            }

            $loader->load('sessions/zombie.xml');
        }
        if (isset($config['selenium'])) {
            if (!class_exists('Behat\\Mink\\Driver\\SeleniumDriver')) {
                throw new \RuntimeException(
                    'Install MinkSeleniumDriver in order to activate selenium session.'
                );
            }

            $loader->load('sessions/selenium.xml');
        }
        if (isset($config['selenium2'])) {
            if (!class_exists('Behat\\Mink\\Driver\\Selenium2Driver')) {
                throw new \RuntimeException(
                    'Install MinkSelenium2Driver in order to activate selenium2 session.'
                );
            }

            $loader->load('sessions/selenium2.xml');
        }

        $minkParameters = array();
        foreach ($config as $ns => $tlValue) {
            if (!is_array($tlValue)) {
                $minkParameters[$ns] = $tlValue;
            } else {
                foreach ($tlValue as $name => $value) {
                    if ('guzzle_parameters' === $name) {
                        $value['redirect.disable'] = true;
                    }

                    $container->setParameter("behat.mink.$ns.$name", $value);
                }
            }
        }
        $container->setParameter('behat.mink.parameters', $minkParameters);

        if (isset($config['base_url'])) {
            $container->setParameter('behat.mink.base_url', $config['base_url']);
        }
        $container->setParameter('behat.mink.default_session', $config['default_session']);
        $container->setParameter('behat.mink.javascript_session', $config['javascript_session']);
        $container->setParameter('behat.mink.browser_name', $config['browser_name']);

        $minkReflection = new \ReflectionClass('Behat\Mink\Mink');
        $minkLibPath    = realpath(dirname($minkReflection->getFilename()) . '/../../../');
        $container->setParameter('mink.paths.lib', $minkLibPath);

        if ($config['show_auto']) {
            $loader->load('failure_show_listener.xml');
        }
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $config = $this->loadEnvironmentConfiguration();

        $builder->
            children()->
                scalarNode('mink_loader')->
                    defaultValue(isset($config['mink_loader']) ? $config['mink_loader'] : null)->
                end()->
                scalarNode('base_url')->
                    defaultValue(isset($config['base_url']) ? $config['base_url'] : null)->
                end()->
                scalarNode('files_path')->
                    defaultValue(isset($config['files_path']) ? $config['files_path'] : null)->
                end()->
                booleanNode('show_auto')->
                    defaultValue(isset($config['show_auto']) ? 'true' === $config['show_auto'] : false)->
                end()->
                scalarNode('show_cmd')->
                    defaultValue(isset($config['show_cmd']) ? $config['show_cmd'] : null)->
                end()->
                scalarNode('show_tmp_dir')->
                    defaultValue(isset($config['show_tmp_dir']) ? $config['show_tmp_dir'] : sys_get_temp_dir())->
                end()->
                scalarNode('default_session')->
                    defaultValue(isset($config['default_session']) ? $config['default_session'] : 'goutte')->
                end()->
                scalarNode('javascript_session')->
                    defaultValue(isset($config['javascript_session']) ? $config['javascript_session'] : 'selenium2')->
                end()->
                scalarNode('browser_name')->
                    defaultValue(isset($config['browser_name']) ? $config['browser_name'] : 'firefox')->
                end()->
                arrayNode('goutte')->
                    children()->
                        arrayNode('server_parameters')->
                            useAttributeAsKey('key')->
                            prototype('variable')->end()->
                        end()->
                        arrayNode('guzzle_parameters')->
                            useAttributeAsKey('key')->
                            prototype('variable')->end()->
                        end()->
                    end()->
                end()->
                arrayNode('sahi')->
                    children()->
                        scalarNode('sid')->
                            defaultValue(isset($config['sahi']['sid']) ? $config['sahi']['sid'] : null)->
                        end()->
                        scalarNode('host')->
                            defaultValue('localhost')->
                        end()->
                        scalarNode('port')->
                            defaultValue(isset($config['sahi']['port']) ? $config['sahi']['port'] : 9999)->
                        end()->
                    end()->
                end()->
                arrayNode('zombie')->
                    children()->
                        scalarNode('host')->
                            defaultValue(isset($config['zombie']['host']) ? $config['zombie']['host'] : '127.0.0.1')->
                        end()->
                        scalarNode('port')->
                            defaultValue(isset($config['zombie']['port']) ? $config['zombie']['port'] : 8124)->
                        end()->
                        scalarNode('auto_server')->
                            defaultValue(isset($config['zombie']['auto_server']) ? $config['zombie']['auto_server'] : true)->
                        end()->
                        scalarNode('node_bin')->
                            defaultValue(isset($config['zombie']['node_bin']) ? $config['zombie']['node_bin'] : 'node')->
                        end()->
                    end()->
                end()->
                arrayNode('selenium')->
                    children()->
                        scalarNode('host')->
                            defaultValue(isset($config['selenium']['host']) ? $config['selenium']['host'] : '127.0.0.1')->
                        end()->
                        scalarNode('port')->
                            defaultValue(isset($config['selenium']['port']) ? $config['selenium']['port'] : 4444)->
                        end()->
                        scalarNode('browser')->
                            defaultValue(isset($config['selenium']['browser']) ? $config['selenium']['browser'] : '*%behat.mink.browser_name%')->
                        end()->
                    end()->
                end()->
                arrayNode('selenium2')->
                    children()->
                        scalarNode('browser')->
                            defaultValue(isset($config['selenium2']['browser']) ? $config['selenium2']['browser'] : '%behat.mink.browser_name%')->
                        end()->
                        arrayNode('capabilities')->
                            children()->
                                scalarNode('browserName')->
                                    defaultValue(isset($config['selenium2']['capabilities']['browserName']) ? $config['selenium2']['capabilities']['browserName'] : 'firefox')->
                                end()->
                                scalarNode('version')->
                                    defaultValue(isset($config['selenium2']['capabilities']['version']) ? $config['selenium2']['capabilities']['version'] : "9")->
                                end()->
                                scalarNode('platform')->
                                    defaultValue(isset($config['selenium2']['capabilities']['platform']) ? $config['selenium2']['capabilities']['platform'] : 'ANY')->
                                end()->
                                scalarNode('browserVersion')->
                                    defaultValue(isset($config['selenium2']['capabilities']['browserVersion']) ? $config['selenium2']['capabilities']['browserVersion'] : "9")->
                                end()->
                                scalarNode('browser')->
                                    defaultValue(isset($config['selenium2']['capabilities']['browser']) ? $config['selenium2']['capabilities']['browser'] : 'firefox')->
                                end()->
                                scalarNode('name')->
                                    defaultValue(isset($config['selenium2']['capabilities']['name']) ? $config['selenium2']['capabilities']['name'] : 'Behat Test')->
                                end()->
                                scalarNode('deviceOrientation')->
                                    defaultValue(isset($config['selenium2']['capabilities']['deviceOrientation']) ? $config['selenium2']['capabilities']['deviceOrientation'] : 'portrait')->
                                end()->
                                scalarNode('deviceType')->
                                    defaultValue(isset($config['selenium2']['capabilities']['deviceType']) ? $config['selenium2']['capabilities']['deviceType'] : 'tablet')->
                                end()->
                                booleanNode('javascriptEnabled')->end()->
                                booleanNode('databaseEnabled')->end()->
                                booleanNode('locationContextEnabled')->end()->
                                booleanNode('applicationCacheEnabled')->end()->
                                booleanNode('browserConnectionEnabled')->end()->
                                booleanNode('webStorageEnabled')->end()->
                                booleanNode('rotatable')->end()->
                                booleanNode('acceptSslCerts')->end()->
                                booleanNode('nativeEvents')->end()->
                                arrayNode('proxy')->
                                    children()->
                                        scalarNode('proxyType')->end()->
                                        scalarNode('proxyAuthconfigUrl')->end()->
                                        scalarNode('ftpProxy')->end()->
                                        scalarNode('httpProxy')->end()->
                                        scalarNode('sslProxy')->end()->
                                    end()->
                                    validate()->
                                        ifTrue(function ($v) {
                                            return empty($v);
                                        })->
                                        thenUnset()->
                                    end()->
                                end()->
                                arrayNode('chrome')->
                                    children()->
                                        arrayNode('switches')->
                                            prototype('scalar')->end()->
                                        end()->
                                        scalarNode('binary')->end()->
                                        arrayNode('extensions')->
                                            prototype('scalar')->end()->
                                        end()->
                                    end()->
                                end()->
                            end()->
                        end()->
                        scalarNode('wd_host')->
                            defaultValue(isset($config['selenium2']['wd_host']) ? $config['selenium2']['wd_host'] : 'http://localhost:4444/wd/hub')->
                        end()->
                    end()->
                end()->
            end()->
        end();
    }

    /**
     * Returns compiler passes used by mink extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return array(
            new Compiler\SelectorsPass(),
            new Compiler\SessionsPass(),
        );
    }

    protected function loadEnvironmentConfiguration()
    {
        $config = array();
        if ($envConfig = getenv('MINK_EXTENSION_PARAMS')) {
            parse_str($envConfig, $config);
        }

        return $config;
    }
}
