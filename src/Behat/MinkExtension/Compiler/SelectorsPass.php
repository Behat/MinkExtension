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
 * Selectors handler compilation pass. Registers all available Mink selector engines.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SelectorsPass implements CompilerPassInterface
{
    /**
     * Registers additional Mink selector handlers.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('behat.mink.selector.handler')) {
            return;
        }

        $handlerDefinition = $container->getDefinition('behat.mink.selector.handler');
        foreach ($container->findTaggedServiceIds('behat.mink.selector') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (isset($attribute['alias']) && $alias = $attribute['alias']) {
                    $handlerDefinition->addMethodCall(
                        'registerSelector', array($alias, new Reference($id))
                    );
                }
            }
        }
    }
}
