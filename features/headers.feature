Feature: Custom Headers
  PhantomJS should have custom header set when requesting a page

  @javascript
  Scenario: Go on http://myhttp.info/
    Given I am on "http://www.xhaus.com/headers"
    Then I should see "Your browser software transmitted the following HTTP headers"
    And I should see "Test-Header" in the "table tbody" element
    And I should see "test_header" in the "table tbody" element
    And I should see "Xhaus-Custom-Header1" in the "table tbody" element
    And I should see "42" in the "table tbody" element
