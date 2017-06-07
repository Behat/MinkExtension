<?php

namespace Behat\MinkExtension\Tests;

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

/**
 * Feature context for testing advanced scenarios.
 */
class FeatureContext extends RawMinkContext
{

    use HttpMockTrait;

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        $this->setUpHttpMock();

        $this->http->mock->when()
          ->methodIs('GET')
          ->pathIs('/foo')
          ->then()
          ->body('Request body')
          ->end();
        $this->http->setUp();
    }

    /**
     * @AfterScenario
     */
    public function tearDown()
    {
        static::tearDownHttpMockAfterClass();
        $this->tearDownHttpMock();
    }

    /**
     * @BeforeScenario @MockXdebug
     */
    public function setUpXdebugMock()
    {
        $_SERVER['XDEBUG_CONFIG'] = 'xdebug';
    }

    /**
     * Mocking the phpunit assertion as it is used by HttpMockTrait.
     */
    public static function assertSame($message, $argument_1, $argument_2)
    {
        return $argument_1 !== $argument_2;
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
