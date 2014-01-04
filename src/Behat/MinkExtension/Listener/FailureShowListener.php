<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Listener;

use Behat\Behat\Tester\Event\StepTested;
use Behat\Testwork\Tester\Result\TestResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Behat\Mink\Mink;
use Behat\Mink\Exception\Exception as MinkException;

/**
 * Failed step response show listener.
 * Listens to failed Behat steps and shows last response in a browser.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FailureShowListener implements EventSubscriberInterface
{
    private $mink;
    private $parameters;

    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(Mink $mink, array $parameters)
    {
        $this->mink       = $mink;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            StepTested::AFTER => array('showFailedStepResponse', -10)
        );
    }

    /**
     * Shows last response of failed step with preconfigured command.
     * Configuration is based on `behat.yml`:
     *
     * `show_auto` enable this listener (default to false)
     * `show_cmd` command to run (`open %s` to open default browser on Mac)
     * `show_tmp_dir` folder where to store temp files (default is system temp)
     *
     * @param StepTested $event
     */
    public function showFailedStepResponse(StepTested $event)
    {
        if (TestResult::FAILED !== $event->getResultCode()) {
            return;
        }

        if (!$event->getTestResult()->getException() instanceof MinkException) {
            return;
        }

        if (null === $this->parameters['show_cmd']) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: open %s")');
        }

        $filename = rtrim($this->parameters['show_tmp_dir'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->mink->getSession()->getPage()->getContent());
        system(sprintf($this->parameters['show_cmd'], escapeshellarg($filename)));
    }
}
