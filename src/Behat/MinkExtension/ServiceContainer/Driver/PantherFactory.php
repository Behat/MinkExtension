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

/**
 * @author Robert Freigang <robertfreigang@gmx.de>
 */
class PantherFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'panther';
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
                ->arrayNode('options')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                    ->info(
                        "These are the options passed as first argument to PantherTestcaseTrait::createPantherClient client constructor."
                    )
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\PantherDriver')) {
            throw new \RuntimeException(
                'Install MinkPantherDriver in order to use panther driver.'
            );
        }

        return new Definition(
            'Behat\Mink\Driver\PantherDriver',
            array(
                $config['options']
            )
        );
    }
}
