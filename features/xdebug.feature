Feature: Xdebug
  In order to properly develop with BDD
  As a feature developer
  I need to be able to use my debugger

  Scenario: Xdebug cookie should not be present on normal requests
    Given I am on "/foo"
    Then I should not have the "XDEBUG_SESSION_START" cookie

  @xdebug
  Scenario: Xdebug cookie should be passed on to requests
    Given I am on "/foo"
    Then I should have the "XDEBUG_SESSION_START" cookie with value "xdebug"

  Scenario: When running php with xdebug from the command line the cookie should be set
    Given I am on "/foo"
    Then I should have the "XDEBUG_SESSION_START" cookie with value "xdebug"
