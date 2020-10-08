<?php

namespace Behat\MinkExtension\ServiceContainer\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Reference;

class Symfony2Factory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'symfony2';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Symfony2Extension\Driver\KernelDriver')) {
            throw new \RuntimeException(
                'Install Symfony2Extension in order to activate symfony browserkit driver.'
            );
        }

        return new Reference('Behat\Symfony2Extension\Driver\KernelDriver');
    }
}
