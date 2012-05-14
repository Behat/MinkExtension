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
            $loader->load('sessions/goutte.xml');
        }
        if (isset($config['sahi'])) {
            $loader->load('sessions/sahi.xml');
        }
        if (isset($config['zombie'])) {
            $loader->load('sessions/zombie.xml');
        }
        if (isset($config['selenium'])) {
            $loader->load('sessions/selenium.xml');
        }
        if (isset($config['selenium2'])) {
            $loader->load('sessions/selenium2.xml');
        }

        $minkParameters = array();
        foreach ($config as $ns => $tlValue) {
            if (!is_array($tlValue)) {
                $minkParameters[$ns] = $tlValue;
            } else {
                foreach ($tlValue as $name => $value) {
                    $container->setParameter("behat.mink.$ns.$name", $value);
                }
            }
        }
        $container->setParameter('behat.mink.parameters', $minkParameters);

        $minkReflection = new \ReflectionClass('Behat\Mink\Mink');
        $minkLibPath    = realpath(dirname($minkReflection->getFilename()) . '/../../../');
        $container->setParameter('mink.paths.lib', $minkLibPath);
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                scalarNode('mink_loader')->
                    defaultNull()->
                end()->
                scalarNode('base_url')->
                    defaultNull()->
                end()->
                scalarNode('files_path')->
                    defaultNull()->
                end()->
                scalarNode('show_cmd')->
                    defaultNull()->
                end()->
                scalarNode('show_tmp_dir')->
                    defaultValue(sys_get_temp_dir())->
                end()->
                scalarNode('default_session')->
                    defaultNull()->
                end()->
                scalarNode('javascript_session')->
                    defaultNull()->
                end()->
                scalarNode('browser_name')->
                    defaultNull()->
                end()->
                arrayNode('goutte')->
                    children()->
                        arrayNode('zend_config')->
                            useAttributeAsKey('key')->
                            prototype('variable')->end()->
                        end()->
                        arrayNode('server_parameters')->
                            useAttributeAsKey('key')->
                            prototype('variable')->end()->
                        end()->
                    end()->
                end()->
                arrayNode('sahi')->
                    children()->
                        scalarNode('sid')->
                            defaultNull()->
                        end()->
                        scalarNode('host')->
                            defaultValue('localhost')->
                        end()->
                        scalarNode('port')->
                            defaultValue(9999)->
                        end()->
                    end()->
                end()->
                arrayNode('zombie')->
                    children()->
                        scalarNode('host')->
                            defaultValue('127.0.0.1')->
                        end()->
                        scalarNode('port')->
                            defaultValue(8124)->
                        end()->
                        scalarNode('auto_server')->
                            defaultValue(true)->
                        end()->
                        scalarNode('node_bin')->
                            defaultValue('node')->
                        end()->
                    end()->
                end()->
                arrayNode('selenium')->
                    children()->
                        scalarNode('host')->
                            defaultValue('127.0.0.1')->
                        end()->
                        scalarNode('port')->
                            defaultValue(4444)->
                        end()->
                        scalarNode('browser')->
                            defaultValue('*%behat.mink.browser_name%')->
                        end()->
                    end()->
                end()->
                arrayNode('selenium2')->
                    children()->
                        scalarNode('browser')->
                            defaultValue('%behat.mink.browser_name%')->
                        end()->
                        arrayNode('capabilities')->
                            children()->
                                scalarNode('browserName')->
                                    defaultValue('firefox')->
                                end()->
                                scalarNode('version')->
                                    defaultValue(8)->
                                end()->
                                scalarNode('platform')->
                                    defaultValue('ANY')->
                                end()->
                                scalarNode('browserVersion')->
                                    defaultValue(8)->
                                end()->
                                scalarNode('browser')->
                                    defaultValue('firefox')->
                                end()->
                            end()->
                        end()->
                        scalarNode('wd_host')->
                            defaultValue('http://localhost:4444/wd/hub')->
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
}
