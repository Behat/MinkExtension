<?php

namespace Behat\MinkExtension;

use Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Config\FileLocator,
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
        $processor     = new Processor();
        $configuration = new Configuration();
        $loader        = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));

        $config = $processor->processConfiguration($configuration, array($config));
        $loader->load('mink.xml');

        if (isset($config['mink_loader'])) {
            $configPath = $container->getParameter('behat.paths.config');

            if (file_exists($configPath.DIRECTORY_SEPARATOR.$config['mink_loader'])) {
                require($configPath.DIRECTORY_SEPARATOR.$config['mink_loader']);
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
