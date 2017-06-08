<?php

namespace Behat\MinkExtension\Tests;

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Feature context for testing advanced scenarios.
 */
class FeatureContext extends RawMinkContext
{

    /**
     * @BeforeScenario @MockXdebug
     */
    public function setUpXdebugMock()
    {
        $_SERVER['XDEBUG_CONFIG'] = 'xdebug';
    }

    /**
     * @Then /^I should have the "([^"]*)" cookie with value "([^"]*)"$/
     */
    public function iShouldHaveTheCookieWithValue($cookieName, $cookieExpectedValue)
    {
        $this->assertSession()->cookieEquals($cookieName, $cookieExpectedValue);
    }

    /**
     * @Then /^I should not have the "([^"]*)" cookie$/
     */
    public function iShouldNotHaveTheCookie($cookieName)
    {
        if ($this->getSession()->getCookie($cookieName)) {
            throw new ExpectationException(
              'The cookie with name ' . $cookieName . ' was not found, but it should not be present.',
              $this->getSession()
            );
        }
    }

}
