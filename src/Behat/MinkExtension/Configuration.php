<?php

namespace Behat\MinkExtension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/*
 * This file is part of the Behat\MinkExtension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * MinkExtension configuration tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Returns configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder->root('mink')->
            children()->
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
                    defaultValue('goutte')->
                end()->
                scalarNode('javascript_session')->
                    defaultValue('sahi')->
                end()->
                scalarNode('browser_name')->
                    defaultValue('firefox')->
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
}
