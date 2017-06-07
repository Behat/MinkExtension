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
    public function iShouldHaveTheCookieWithValue($cookie_name, $cookie_expected_value) {
        if ($cookie_real_value = $this->getSession()->getCookie($cookie_name)) {
            if ($cookie_real_value !== $cookie_expected_value) {
                throw new ExpectationException(
                  'The cookie with name ' . $cookie_name . ' was found, but does not contain ' . $cookie_real_value . ', yet it contains ' . $cookie_expected_value . '.',
                  $this->getSession()
                );
            }
        } else {
            throw new ExpectationException(
              'The cookie with name ' . $cookie_name . ' was not found',
              $this->getSession()
            );
        }
    }

    /**
     * @Then /^I should not have the "([^"]*)" cookie$/
     */
    public function iShouldNotHaveTheCookie($cookie_name)
    {
        if ($cookie_real_value = $this->getSession()->getCookie($cookie_name)) {
            throw new ExpectationException(
              'The cookie with name ' . $cookie_name . ' was not found, but it should not be present.',
              $this->getSession()
            );
        }
    }

}
