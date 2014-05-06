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

class SauceLabsFactory extends Selenium2Factory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'sauce_labs';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('username')->defaultValue(getenv('SAUCE_USERNAME'))->end()
                ->scalarNode('access_key')->defaultValue(getenv('SAUCE_ACCESS_KEY'))->end()
                ->booleanNode('connect')->defaultFalse()->end()
                ->scalarNode('browser')->defaultValue('firefox')->end()
                ->append($this->getCapabilitiesNode())
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        $capabilities = $config['capabilities'];
        $capabilities['tags'] = array(php_uname('n'), 'PHP '.phpversion());

        if (getenv('TRAVIS_JOB_NUMBER')) {
            $capabilities['tunnel-identifier'] = getenv('TRAVIS_JOB_NUMBER');
            $capabilities['build'] = getenv('TRAVIS_BUILD_NUMBER');
            $capabilities['tags'] = array('Travis-CI', 'PHP '.phpversion());
        }
        
        if (getenv('JENKINS_HOME')) {
            $capabilities['tunnel-identifier'] = getenv('JOB_NAME');
            $capabilities['build'] = getenv('BUILD_NUMBER');
            $capabilities['tags'] = array('Jenkins', 'PHP '.phpversion(), getenv('BUILD_TAG'));
        }

        $host = 'ondemand.saucelabs.com';
        if ($config['connect']) {
            $host = 'localhost:4445';
        }

        $config['capabilities'] = $capabilities;
        $config['wd_host'] = sprintf('%s:%s@%s/wd/hub', $config['username'], $config['access_key'], $host);

        return parent::buildDriver($config);
    }

    protected function getCapabilitiesNode()
    {
        $node = parent::getCapabilitiesNode();

        $node
            ->children()
                ->scalarNode('name')->defaultValue('Behat feature suite')->end()
                ->scalarNode('platform')->defaultValue('Linux')->end()
                ->scalarNode('selenium-version')->defaultValue('2.31.0')->end()
                ->scalarNode('max-duration')->defaultValue('300')->end()
                ->scalarNode('command-timeout')->end()
                ->scalarNode('idle-timeout')->end()
                ->scalarNode('build')->info('will be set automatically based on the TRAVIS_JOB_NUMBER environment variable if available')->end()
                ->arrayNode('custom-data')
                    ->useAttributeAsKey('')
                    ->prototype('variable')->end()
                ->end()
                ->scalarNode('screen-resolution')->end()
                ->scalarNode('tunnel-identifier')->end()
                ->arrayNode('prerun')
                    ->children()
                        ->scalarNode('executable')->isRequired()->end()
                        ->arrayNode('args')->prototype('scalar')->end()->end()
                        ->booleanNode('background')->defaultFalse()->end()
                    ->end()
                ->end()
                ->booleanNode('record-video')->end()
                ->booleanNode('record-screenshots')->end()
                ->booleanNode('capture-html')->end()
                ->booleanNode('disable-popup-handler')->end()
            ->end()
        ;

        return $node;
    }
}
