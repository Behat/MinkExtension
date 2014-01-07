<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Exception\ProcessingException;
use Behat\Testwork\ServiceContainer\Extension as BaseExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Mink extension for Behat class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Extension implements BaseExtension
{
    const MINK_ID = 'mink';
    const SELECTORS_HANDLER_ID = 'mink.selectors_handler';

    const SESSION_TAG = 'mink.session';
    const SELECTOR_TAG = 'mink.selector';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        if (isset($config['mink_loader'])) {
            $basePath = $container->getParameter('paths.base');

            if (file_exists($basePath.DIRECTORY_SEPARATOR.$config['mink_loader'])) {
                require($basePath.DIRECTORY_SEPARATOR.$config['mink_loader']);
            } else {
                require($config['mink_loader']);
            }
        }

        $this->loadMink($container);
        $this->loadContextInitializer($container);
        $this->loadSelectorsHandler($container);
        $this->loadSessionsListener($container);

        if ($config['show_auto']) {
            $this->loadFailureShowListener($container);
        }

        if (isset($config['goutte'])) {
            $this->loadGoutteSession($container, $config['goutte']);
            unset($config['goutte']);
        }
        if (isset($config['sahi'])) {
            $this->loadSahiSession($container, $config['sahi']);
            unset($config['sahi']);
        }
        if (isset($config['zombie'])) {
            $this->loadZombieSession($container, $config['zombie']);
            unset($config['zombie']);
        }
        if (isset($config['selenium'])) {
            $this->loadSeleniumSession($container, $config['selenium']);
            unset($config['zombie']);
        }
        if (isset($config['selenium2'])) {
            $this->loadSelenium2Session($container, $config['selenium2']);
            unset($config['selenium2']);
        }
        if (isset($config['saucelabs'])) {
            $this->loadSaucelabsSession($container, $config['saucelabs']);
            unset($config['saucelabs']);
        }

        $container->setParameter('mink.parameters', $config);
        $container->setParameter('mink.base_url', $config['base_url']);
        $container->setParameter('mink.default_session', $config['default_session']);
        $container->setParameter('mink.javascript_session', $config['javascript_session']);
        $container->setParameter('mink.browser_name', $config['browser_name']);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('mink_loader')->defaultNull()->end()
                ->scalarNode('base_url')->defaultNull()->end()
                ->scalarNode('files_path')->defaultNull()->end()
                ->booleanNode('show_auto')->defaultFalse()->end()
                ->scalarNode('show_cmd')->defaultNull()->end()
                ->scalarNode('show_tmp_dir')->defaultValue(sys_get_temp_dir())->end()
                ->scalarNode('default_session')->defaultValue('goutte')->end()
                ->scalarNode('javascript_session')->defaultValue('selenium2')->end()
                ->scalarNode('browser_name')->defaultValue('firefox')->end()
                ->arrayNode('goutte')
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
                ->end()
                ->arrayNode('sahi')
                    ->children()
                        ->scalarNode('sid')->defaultNull()->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultValue(9999)->end()
                        ->scalarNode('browser')->defaultNull()->end()
                        ->scalarNode('limit')->defaultValue(600)->end()
                    ->end()
                ->end()
                ->arrayNode('zombie')
                    ->children()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('port')->defaultValue(8124)->end()
                        ->booleanNode('auto_server')->defaultValue(true)->end()
                        ->scalarNode('node_bin')->defaultValue('node')->end()
                        ->scalarNode('server_path')->defaultNull()->end()
                        ->scalarNode('threshold')->defaultValue(2000000)->end()
                        ->scalarNode('node_modules_path')->defaultValue('')->end()
                    ->end()
                ->end()
                ->arrayNode('selenium')
                    ->children()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('port')->defaultValue(4444)->end()
                        ->scalarNode('browser')->defaultValue('*%mink.browser_name%')->end()
                    ->end()
                ->end()
                ->arrayNode('selenium2')
                    ->children()
                        ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
                        ->arrayNode('capabilities')
                            ->addDefaultsIfNotSet()
                            ->normalizeKeys(false)
                            ->children()
                                ->scalarNode('browserName')->defaultValue('firefox')->end()
                                ->scalarNode('version')->defaultValue('9')->end()
                                ->scalarNode('platform')->defaultValue('ANY')->end()
                                ->scalarNode('browserVersion')->defaultValue('9')->end()
                                ->scalarNode('browser')->defaultValue('firefox')->end()
                                ->scalarNode('ignoreZoomSetting')->defaultValue('false')->end()
                                ->scalarNode('name')->defaultValue('Behat Test')->end()
                                ->scalarNode('deviceOrientation')->defaultValue('portrait')->end()
                                ->scalarNode('deviceType')->defaultValue('tablet')->end()
                                ->scalarNode('selenium-version')->defaultValue('2.31.0')->end()
                                ->scalarNode('max-duration')->defaultValue('300')->end()
                                ->scalarNode('resolution')->defaultNull()->end()
                                ->booleanNode('javascriptEnabled')->end()
                                ->booleanNode('databaseEnabled')->end()
                                ->booleanNode('locationContextEnabled')->end()
                                ->booleanNode('applicationCacheEnabled')->end()
                                ->booleanNode('browserConnectionEnabled')->end()
                                ->booleanNode('webStorageEnabled')->end()
                                ->booleanNode('rotatable')->end()
                                ->booleanNode('acceptSslCerts')->end()
                                ->booleanNode('nativeEvents')->end()
                                ->booleanNode('passed')->end()
                                ->booleanNode('record-video')->end()
                                ->booleanNode('record-screenshots')->end()
                                ->booleanNode('capture-html')->end()
                                ->booleanNode('disable-popup-handler')->end()
                                ->arrayNode('proxy')
                                    ->children()
                                        ->scalarNode('proxyType')->end()
                                        ->scalarNode('proxyAuthconfigUrl')->end()
                                        ->scalarNode('ftpProxy')->end()
                                        ->scalarNode('httpProxy')->end()
                                        ->scalarNode('sslProxy')->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return empty($v);
                                        })
                                        ->thenUnset()
                                    ->end()
                                ->end()
                                ->arrayNode('firefox')
                                    ->children()
                                        ->scalarNode('profile')
                                            ->validate()
                                                ->ifTrue(function ($v) {
                                                    return !file_exists($v);
                                                })
                                                ->thenInvalid('Cannot find profile zip file %s')
                                            ->end()
                                        ->end()
                                        ->scalarNode('binary')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('chrome')
                                    ->children()
                                        ->arrayNode('switches')->prototype('scalar')->end()->end()
                                        ->scalarNode('binary')->end()
                                        ->arrayNode('extensions')->prototype('scalar')->end()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('wd_host')->defaultValue('http://localhost:4444/wd/hub')->end()
                    ->end()
                ->end()
                ->arrayNode('saucelabs')
                    ->children()
                        ->scalarNode('username')->defaultValue(getenv('SAUCE_USERNAME'))->end()
                        ->scalarNode('access_key')->defaultValue(getenv('SAUCE_ACCESS_KEY'))->end()
                        ->booleanNode('connect')->defaultFalse()->end()
                        ->scalarNode('browser')->defaultValue('firefox')->end()
                        ->arrayNode('capabilities')
                            ->addDefaultsIfNotSet()
                            ->normalizeKeys(false)
                            ->children()
                                ->scalarNode('name')->defaultValue('Behat feature suite')->end()
                                ->scalarNode('platform')->defaultValue('Linux')->end()
                                ->scalarNode('version')->defaultValue('21')->end()
                                ->scalarNode('selenium-version')->defaultValue('2.31.0')->end()
                                ->scalarNode('max-duration')->defaultValue('300')->end()
                                ->scalarNode('deviceType')->defaultNull()->end()
                                ->scalarNode('deviceOrientation')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'mink';
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processSessions($container);
        $this->processSelectors($container);
    }

    private function loadMink(ContainerBuilder $container)
    {
        $container->setDefinition(self::MINK_ID, new Definition('Behat\Mink\Mink'));
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\MinkExtension\Context\Initializer\MinkAwareInitializer', array(
            new Reference(self::MINK_ID),
            '%mink.parameters%',
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('mink.context_initializer', $definition);
    }

    private function loadSelectorsHandler(ContainerBuilder $container)
    {
        $container->setDefinition(self::SELECTORS_HANDLER_ID, new Definition('Behat\Mink\Selector\SelectorsHandler'));

        $cssSelectorDefinition = new Definition('Behat\Mink\Selector\CssSelector');
        $cssSelectorDefinition->addTag(self::SELECTOR_TAG, array('alias' => 'css'));
        $container->setDefinition(self::SELECTOR_TAG . '.css', $cssSelectorDefinition);

        $namedSelectorDefinition = new Definition('Behat\Mink\Selector\NamedSelector');
        $namedSelectorDefinition->addTag(self::SELECTOR_TAG, array('alias' => 'named'));
        $container->setDefinition(self::SELECTOR_TAG . '.named', $namedSelectorDefinition);
    }

    private function loadSessionsListener(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\MinkExtension\Listener\SessionsListener', array(
            new Reference(self::MINK_ID),
            '%mink.parameters%',
        ));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('mink.listener.sessions', $definition);
    }

    private function loadFailureShowListener(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\MinkExtension\Listener\FailureShowListener', array(
            new Reference(self::MINK_ID),
            '%mink.parameters%',
        ));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('mink.listener.failure_show', $definition);
    }

    private function loadGoutteSession(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\GoutteDriver')) {
            throw new \RuntimeException(
                'Install MinkGoutteDriver in order to activate goutte session.'
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

        $driverDefinition = new Definition('Behat\Mink\Driver\GoutteDriver', array(
            $clientDefinition,
        ));
        $this->loadSession($container, $driverDefinition, 'goutte');
    }

    private function loadSahiSession(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\SahiDriver')) {
            throw new \RuntimeException(
                'Install MinkSahiDriver in order to activate sahi session.'
            );
        }

        $driverDefinition = new Definition('Behat\Mink\Driver\SahiDriver', array(
            '%mink.browser_name%',
            new Definition('Behat\SahiClient\Client', array(
                new Definition('Behat\SahiClient\Connection', array(
                    $config['sid'],
                    $config['host'],
                    $config['port'],
                    $config['browser'],
                    $config['limit'],
                )),
            )),
        ));
        $this->loadSession($container, $driverDefinition, 'sahi');
    }

    private function loadZombieSession(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\ZombieDriver')) {
            throw new \RuntimeException(
                'Install MinkZombieDriver in order to activate zombie session.'
            );
        }

        $driverDefinition = new Definition('Behat\Mink\Driver\ZombieDriver', array(
            new Definition('Behat\Mink\Driver\NodeJS\Server\ZombieServer', array(
                $config['host'],
                $config['port'],
                $config['node_bin'],
                $config['server_path'],
                $config['threshold'],
                $config['node_modules_path'],
            )),
            new Definition('Behat\Mink\Driver\NodeJS\Connection', array(
                $config['host'],
                $config['port'],
            )),
            $config['auto_server'],
        ));
        $this->loadSession($container, $driverDefinition, 'zombie');
    }

    private function loadSeleniumSession(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\SeleniumDriver')) {
            throw new \RuntimeException(
                'Install MinkSeleniumDriver in order to activate selenium session.'
            );
        }

        $driverDefinition = new Definition('Behat\Mink\Driver\SeleniumDriver', array(
            $config['browser'],
            '%mink.base_url%',
            new Definition('Selenium\Client', array(
                $config['host'],
                $config['port'],
            )),
        ));
        $this->loadSession($container, $driverDefinition, 'selenium');
    }

    private function loadSelenium2Session(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(
                'Install MinkSelenium2Driver in order to activate selenium2 session.'
            );
        }

        $driverDefinition = new Definition('Behat\Mink\Driver\Selenium2Driver', array(
            $config['browser'],
            $config['capabilities'],
            $config['wd_host'],
        ));
        $this->loadSession($container, $driverDefinition, 'selenium2');
    }

    private function loadSaucelabsSession(ContainerBuilder $container, array $config)
    {
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(
                'Install MinkSelenium2Driver in order to activate saucelabs session.'
            );
        }
        $capabilities = $config['capabilities'];
        $capabilities['tags'] = array(php_uname('n'), 'PHP '.phpversion());

        if (getenv('TRAVIS_JOB_NUMBER')) {
            $capabilities['tunnel-identifier'] = getenv('TRAVIS_JOB_NUMBER');
            $capabilities['build'] = getenv('TRAVIS_BUILD_NUMBER');
            $capabilities['tags'] = array('Travis-CI', 'PHP '.phpversion());
        }

        $host = 'ondemand.saucelabs.com';
        if ($config['connect']) {
            $host = 'localhost:4445';
        }

        $driverDefinition = new Definition('Behat\Mink\Driver\Selenium2Driver', array(
            $config['browser'],
            $capabilities,
            sprintf('%s:%s@%s/wd/hub', $config['username'], $config['access_key'], $host),
        ));
        $this->loadSession($container, $driverDefinition, 'saucelabs');
    }

    private function loadSession(ContainerBuilder $container, Definition $driverDefinition, $alias)
    {
        $definition = new Definition('Behat\Mink\Session', array(
            $driverDefinition,
            new Reference(self::SELECTORS_HANDLER_ID),
        ));
        $definition->addTag(self::SESSION_TAG, array('alias' => $alias));
        $container->setDefinition(self::SESSION_TAG . '.' . $alias, $definition);
    }

    private function processSessions(ContainerBuilder $container)
    {
        $handlerDefinition = $container->getDefinition(self::MINK_ID);

        foreach ($container->findTaggedServiceIds(self::SESSION_TAG) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['alias'])) {
                    throw new ProcessingException(sprintf(
                        'All `%s` tags should have an `alias` attribute, but `%s` service has none.',
                        $tag,
                        $id
                    ));
                }
                $handlerDefinition->addMethodCall(
                    'registerSession', array($tag['alias'], new Reference($id))
                );
            }
        }
    }

    private function processSelectors(ContainerBuilder $container)
    {
        $handlerDefinition = $container->getDefinition(self::SELECTORS_HANDLER_ID);

        foreach ($container->findTaggedServiceIds(self::SELECTOR_TAG) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['alias'])) {
                    throw new ProcessingException(sprintf(
                        'All `%s` tags should have an `alias` attribute, but `%s` service has none.',
                        $tag,
                        $id
                    ));
                }
                $handlerDefinition->addMethodCall(
                    'registerSelector', array($tag['alias'], new Reference($id))
                );
            }
        }
    }
}
