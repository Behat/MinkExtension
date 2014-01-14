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
 * @author Christophe Coevoet <stof@notk.org>
 */
class GoutteFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'goutte';
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
        $builder
            ->children()
                ->arrayNode('server_parameters')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('guzzle_parameters')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                    ->validate()
                        ->always()
                        ->then(function ($v) {
                            $v['redirect.disable'] = true;

                            return $v;
                        })
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
        if (!class_exists('Behat\Mink\Driver\GoutteDriver')) {
            throw new \RuntimeException(
                'Install MinkGoutteDriver in order to use goutte driver.'
            );
        }

        $clientDefinition = new Definition('Behat\Mink\Driver\Goutte\Client', array(
            $config['server_parameters'],
        ));
        $clientDefinition->addMethodCall('setClient', array(
            new Definition('Guzzle\Http\Client', array(
                null,
                $config['guzzle_parameters'],
            )),
        ));

        return new Definition('Behat\Mink\Driver\GoutteDriver', array(
            $clientDefinition,
        ));
    }
}
