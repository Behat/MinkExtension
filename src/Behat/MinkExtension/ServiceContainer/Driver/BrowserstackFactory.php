<?php
namespace Behat\MinkExtension\ServiceContainer\Driver;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class BrowserstackFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'browserstack';
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
            ->
                    children()->
                        scalarNode('username')->
                            defaultValue(getenv('BROWSERSTACK_USERNAME'))->
                        end()->
                        scalarNode('access_key')->
                            defaultValue(getenv('BROWSERSTACK_ACCESS_KEY'))->
                        end()->
                        scalarNode('browser')->
                            defaultValue('firefox')->
                        end()->
                        booleanNode('tunnel')->
                            defaultFalse()->
                        end()->
                        booleanNode('debug')->
                            defaultFalse()->
                        end()->
                        arrayNode('capabilities')->
                            children()->
                                scalarNode('name')->
                                    defaultValue('Behat feature suite')->
                                end()->
                                scalarNode('project')->
                                    defaultNull()->
                                end()->
                                scalarNode('build')->
                                    defaultNull()->
                                end()->
                                scalarNode('platform')->
                                    defaultValue('Linux')->
                                end()->
                                scalarNode('version')->
                                    defaultValue('21')->
                                end()->
                                scalarNode('os')->
                                    defaultNull()->
                                end()->
                                scalarNode('os_version')->
                                    defaultNull()->
                                end()->
                                scalarNode('device')->
                                    defaultNull()->
                                end()->
                                scalarNode('acceptSslCerts')->
                                    defaultNull()->
                                end()->
                            end()->
                        end()->
                    end()->
                end()->
            end()->
        end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(
                'Install MinkSelenium2Driver in order to use browserstack driver.'
            );
        }
        $capabilities = $config['capabilities'];
        $capabilities['tags'] = array(php_uname('n'), 'PHP '.phpversion());

        if (getenv('TRAVIS_JOB_NUMBER')) {
            $capabilities['tunnel-identifier'] = getenv('TRAVIS_JOB_NUMBER');
            $capabilities['build'] = getenv('TRAVIS_BUILD_NUMBER');
            $capabilities['tags'] = array('Travis-CI', 'PHP '.phpversion());
        }

        $host = 'hub.browserstack.com';
        if ($config['connect']) {
            $host = 'localhost:4445';
        }

        return new Definition('Behat\Mink\Driver\Selenium2Driver', array(
            $config['browser'],
            $capabilities,
            sprintf('%s:%s@%s/wd/hub', $config['username'], $config['access_key'], $host),
        ));
    }
}
