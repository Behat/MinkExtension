<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Behat\Mink container compilation pass.
 * Registers all available in controller Mink sessions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SessionsPass implements CompilerPassInterface
{
    /**
     * Registers Mink sessions.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('behat.mink')) {
            return;
        }
        $minkDefinition = $container->getDefinition('behat.mink');

        foreach ($container->findTaggedServiceIds('behat.mink.session') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (isset($attribute['alias']) && $name = $attribute['alias']) {
                    $minkDefinition->addMethodCall(
                        'registerSession', array($name, new Reference($id))
                    );
                }
            }
        }

        $minkDefinition->addMethodCall(
            'setDefaultSessionName', array($container->getParameter('behat.mink.default_session'))
        );
    }
}
