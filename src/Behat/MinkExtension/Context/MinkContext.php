<?php

/*
 * This file is part of the Behat MinkExtension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\MinkExtension\Context;

use Behat\Behat\Context\TranslatableContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Mink context for Behat BDD tool.
 * Provides Mink integration and base step definitions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MinkContext extends RawMinkContext implements TranslatableContext
{
    /**
     * Opens homepage.
     *
     * @Given /^(?:|I )am on (?:|the )homepage$/
     * @When /^(?:|I )go to (?:|the )homepage$/
     *
     * Example: Given I am on the homepage
     * Example: And I am on the homepage
     * Example: When I go to the homepage
     *
     */
    public function iAmOnHomepage()
    {
        $this->visitPath('/');
    }

    /**
     * Opens specified page.
     *
     * @Given /^(?:|I )am on "(?P<page>[^"]+)"$/
     * @When /^(?:|I )go to "(?P<page>[^"]+)"$/
     *
     * Example: Given I am on "/articles/isBatmanBruceWayne"
     * Example: And I am on "/articles/isBatmanBruceWayne"
     * Example: When I go to "/articles/isBatmanBruceWayne"
     *
     */
    public function visit($page)
    {
        $this->visitPath($page);
    }

    /**
     * Reloads current page.
     *
     * @When /^(?:|I )reload the page$/
     *
     * Example: And I relod the page
     * Example: When I reload the page
     *
     */
    public function reload()
    {
        $this->getSession()->reload();
    }

    /**
     * Moves backward one page in history.
     *
     * @When /^(?:|I )move backward one page$/
     *
     * Example: And I move backward one page
     * Example: When I move backward one page
     *
     */
    public function back()
    {
        $this->getSession()->back();
    }

    /**
     * Moves forward one page in history
     *
     * @When /^(?:|I )move forward one page$/
     *
     * Example: And I move forward one page
     * Example: When I move forward one page
     *
     */
    public function forward()
    {
        $this->getSession()->forward();
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @When /^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/
     *
     * Example: And I press "Log In"
     * Example: When I press "sign-in"
     *
     */
    public function pressButton($button)
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/
     *
     * Example: And I follow "Log In"
     * Example: When I follow "sign-in"
     *
     */
    public function clickLink($link)
    {
        $link = $this->fixStepArgument($link);
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/
     * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
     *
     * Example: And I fill in "username" with "bruceWayne"
     * Example: And I fill in "username" with: bruceWayne
     * Example: And I fill in "bruceWayne" for "username"
     * Example: When I fill in "username" with "bruceWayne"
     * Example: When I fill in "username" with: bruceWayne
     * Example: When I fill in "bruceWayne" for "username"
     *
     */
    public function fillField($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Fills in form fields with provided table.
     *
     * @When /^(?:|I )fill in the following:$/
     *
     * Example: And I fill in the following:
     *              | userId | 27 |
     *              | username | bruceWayne |
     *              | password | iLoveBats123 |
     * Example: When I follow "sign-in"
     *              | userId | 27 |
     *              | username | bruceWayne |
     *              | password | iLoveBats123 |
     *
     */
    public function fillFields(TableNode $fields)
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->fillField($field, $value);
        }
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @When /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     *
     * Example: And I select "male" from "gender"
     * Example: When I select "VISA" from "paymentType"
     *
     */
    public function selectOption($select, $option)
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * Selects additional option in select field with specified id|name|label|value.
     *
     * @When /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     *
     * Example: And I additionally select "female" from "gender"
     * Example: When I additionally select "AMEX" from "paymentType"
     *
     */
    public function additionallySelectOption($select, $option)
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @When /^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/
     *
     * Example: And I check "Mac OS X" from "os"
     * Example: When I check "Batman" from "heroes"
     *
     */
    public function checkOption($option)
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->checkField($option);
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     *
     * @When /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/
     *
     * Example: And I uncheck "Mac OS X" from "os"
     * Example: When I uncheck "Batman" from "heroes"
     *
     */
    public function uncheckOption($option)
    {
        $option = $this->fixStepArgument($option);
        $this->getSession()->getPage()->uncheckField($option);
    }

    /**
     * Attaches file to field with specified id|name|label|value.
     *
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
     *
     * Example: And I check "Mac OS X" from "os"
     * Example: When I check "Batman" from "heroes"
     *
     */
    public function attachFileToField($field, $path)
    {
        $field = $this->fixStepArgument($field);

        if ($this->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }

        $this->getSession()->getPage()->attachFileToField($field, $path);
    }

    /**
     * Checks, that current page PATH is equal to specified.
     *
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     *
     * Example: And I should be on "/heroes/Batman"
     * Example: Then I should be on "/heroes/Batman"
     *
     */
    public function assertPageAddress($page)
    {
        $this->assertSession()->addressEquals($this->locatePath($page));
    }

    /**
     * Checks, that current page is the homepage.
     *
     * @Then /^(?:|I )should be on (?:|the )homepage$/
     *
     * Example: And I should be on the homepage
     * Example: Then I should be on the hompage
     *
     */
    public function assertHomepage()
    {
        $this->assertSession()->addressEquals($this->locatePath('/'));
    }

    /**
     * Checks, that current page PATH matches regular expression.
     *
     * @Then /^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/
     *
     * Example: And the url should match "/heroes/\w+"
     * Example: Then the url should match "/heroes/\w+"
     *
     */
    public function assertUrlRegExp($pattern)
    {
        $this->assertSession()->addressMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks, that current page response status is equal to specified.
     *
     * @Then /^the response status code should be (?P<code>\d+)$/
     *
     * Example: And the url should match "/heroes/\w+"
     * Example: Then the url should match "/heroes/\w+"
     *
     */
    public function assertResponseStatus($code)
    {
        $this->assertSession()->statusCodeEquals($code);
    }

    /**
     * Checks, that current page response status is not equal to specified.
     *
     * @Then /^the response status code should not be (?P<code>\d+)$/
     *
     * Example: And the response status code should not be 404
     * Example: Then the response status code should not be 404
     *
     */
    public function assertResponseStatusIsNot($code)
    {
        $this->assertSession()->statusCodeNotEquals($code);
    }

    /**
     * Checks, that page contains specified text.
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
     *
     * Example: And I should see "This just in, Bruce Wayne is not Batman"
     * Example: Then I should see "This just in, Bruce Wayne is not Batman"
     *
     */
    public function assertPageContainsText($text)
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that page doesn't contain specified text.
     *
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
     *
     * Example: And I should not see "This just in, Bruce Wayne is Batman"
     * Example: Then I should not see "This just in, Bruce Wayne is Batman"
     *
     */
    public function assertPageNotContainsText($text)
    {
        $this->assertSession()->pageTextNotContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that page contains text matching specified pattern.
     *
     * @Then /^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/
     *
     * Example: And I should not see "This just in, Bruce Wayne is Batman"
     * Example: Then I should not see "This just in, Bruce Wayne is Batman"
     *
     */
    public function assertPageMatchesText($pattern)
    {
        $this->assertSession()->pageTextMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks, that page doesn't contain text matching specified pattern.
     *
     * @Then /^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/
     *
     * Example: And I should not see text matching "Bruce Wayne is Batman"
     * Example: Then I should not see text matching "Bruce Wayne is Batman"
     *
     */
    public function assertPageNotMatchesText($pattern)
    {
        $this->assertSession()->pageTextNotMatches($this->fixStepArgument($pattern));
    }

    /**
     * Checks, that HTML response contains specified string.
     *
     * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
     *
     * Example: And the response should contain "<noscript>Sorry turn JavaScript on to view full experience</noscript>"
     * Example: Then the response should contain "<noscript>Sorry turn JavaScript on to view full experience</noscript>"
     *
     */
    public function assertResponseContains($text)
    {
        $this->assertSession()->responseContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that HTML response doesn't contain specified string.
     *
     * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
     *
     * Example: And the response should not contain "v1.0.1"
     * Example: Then the response should not contain "v1.0.1"
     *
     */
    public function assertResponseNotContains($text)
    {
        $this->assertSession()->responseNotContains($this->fixStepArgument($text));
    }

    /**
     * Checks, that element with specified CSS contains specified text.
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     *
     * Example: And I should see "Batman is dead?" in the "headline" element
     * Example: Then I should see "Batman is dead?" in the "headline" element
     *
     */
    public function assertElementContainsText($element, $text)
    {
        $this->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks, that element with specified CSS doesn't contain specified text.
     *
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     *
     * Example: And I should not see "Batman is alive?" in the "headline" element
     * Example: Then I should not see "Batman is alive?" in the "headline" element
     *
     */
    public function assertElementNotContainsText($element, $text)
    {
        $this->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));
    }

    /**
     * Checks, that element with specified CSS contains specified HTML.
     *
     * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
     *
     * Example: And the "nav" element should contain "profile"
     * Example: Then the "nav" element should contain "profile"
     *
     */
    public function assertElementContains($element, $value)
    {
        $this->assertSession()->elementContains('css', $element, $this->fixStepArgument($value));
    }

    /**
     * Checks, that element with specified CSS doesn't contain specified HTML.
     *
     * @Then /^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
     *
     * Example: And the "nav" element should not contain "logged-in"
     * Example: Then the "nav" element should not contain "logged-in"
     *
     */
    public function assertElementNotContains($element, $value)
    {
        $this->assertSession()->elementNotContains('css', $element, $this->fixStepArgument($value));
    }

    /**
     * Checks, that element with specified CSS exists on page.
     *
     * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element$/
     *
     * Example: And I should see a "canvas" element
     * Example: Then I should see an "apples" element
     *
     */
    public function assertElementOnPage($element)
    {
        $this->assertSession()->elementExists('css', $element);
    }

    /**
     * Checks, that element with specified CSS doesn't exist on page.
     *
     * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
     *
     * Example: And I should not see a "canvas" element
     * Example: Then I should not see an "apples" element
     *
     */
    public function assertElementNotOnPage($element)
    {
        $this->assertSession()->elementNotExists('css', $element);
    }

    /**
     * Checks, that form field with specified id|name|label|value has specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
     *
     * Example: And the "name" field should contain "Bruce Wayne"
     * Example: Then the "name" field should contain "Bruce Wayne"
     *
     */
    public function assertFieldContains($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    /**
     * Checks, that form field with specified id|name|label|value doesn't have specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
     *
     * Example: And the "name" field should not contain "Bruce Wayne"
     * Example: Then the "name" field should not contain "Bruce Wayne"
     *
     */
    public function assertFieldNotContains($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $this->assertSession()->fieldValueNotEquals($field, $value);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is checked.
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/
     *
     * Example: And the "heroes" checkbox should be checked
     * Example: Then the "heroes" checkbox is checked
     *
     */
    public function assertCheckboxChecked($checkbox)
    {
        $this->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/
     *
     * Example: And the "villains" checkbox should be unchecked
     * Example: Then the "villains" checkbox is not checked
     *
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $this->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));
    }

    /**
     * Checks, that (?P<num>\d+) CSS elements exist on the page
     *
     * @Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
     *
     * Example: And I should see 5 "div" elements
     * Example: Then I should see 5 "div" elements
     *
     */
    public function assertNumElements($num, $element)
    {
        $this->assertSession()->elementsCount('css', $element, intval($num));
    }

    /**
     * Prints current URL to console.
     *
     * @Then /^print current URL$/
     *
     * Example: Then print current URL
     *
     */
    public function printCurrentUrl()
    {
        echo $this->getSession()->getCurrentUrl();
    }

    /**
     * Prints last response to console.
     *
     * @Then /^print last response$/
     *
     * Example: Then print last response
     *
     */
    public function printLastResponse()
    {
        echo (
            $this->getSession()->getCurrentUrl()."\n\n".
            $this->getSession()->getPage()->getContent()
        );
    }

    /**
     * Opens last response content in browser.
     *
     * @Then /^show last response$/
     *
     * Example: Then show last responsecd
     *
     */
    public function showLastResponse()
    {
        if (null === $this->getMinkParameter('show_cmd')) {
            throw new \RuntimeException('Set "show_cmd" parameter in behat.yml to be able to open page in browser (ex.: "show_cmd: firefox %s")');
        }

        $filename = rtrim($this->getMinkParameter('show_tmp_dir'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid().'.html';
        file_put_contents($filename, $this->getSession()->getPage()->getContent());
        system(sprintf($this->getMinkParameter('show_cmd'), escapeshellarg($filename)));
    }

    /**
     * Returns list of definition translation resources paths.
     *
     * @return array
     */
    public static function getTranslationResources()
    {
        return self::getMinkTranslationResources();
    }

    /**
     * Returns list of definition translation resources paths for this dictionary.
     *
     * @return array
     */
    public static function getMinkTranslationResources()
    {
        return glob(__DIR__.'/../../../../i18n/*.xliff');
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
