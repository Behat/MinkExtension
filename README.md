MinkExtension
=============

Provides integrartion layer:

* Additional services for Behat (`Mink`, `Sessions`, `Drivers`).
* `Behat\MinkExtension\Context\MinkAwareContextInterface` which provides `Mink`
  instance for your contexts or subcontexts.
* Base `Behat\MinkExtension\Context\MinkContext` context which provides base
  step definitions and hooks for your contexts or subcontexts. Or it could be
  even used as subcontext on its own.

between Behat 2.4+ and Mink 1.4+.

Installation
------------

This extension requires:

* Behat 2.4+
* Mink 1.4+

While there is no stable versions for those packages, you could download betas from:

* [Behat downloads](https://github.com/Behat/Behat/downloads)
* [Mink downloads](https://github.com/Behat/Mink/downloads)

After downloading and placing them into project directory, you need to download and
activate `MinkExtension`:

1. [Download extension](https://github.com/downloads/Behat/MinkExtension/mink_extension.phar)
2. Put downloaded phar package into folder with Behat and Mink
3. Tell Behat about extensions with `behat.yml` configuration:

    ``` yaml
    # behat.yml
    defaults:
      # ...
      extensions:
        mink_extension.phar:
          mink_loader:        'mink-VERSION.phar'
          base_url:           'http://example.com'

          javascript_session: 'selenium2'

          goutte:             ~
          selenium2:          ~
    ```

    For all configuration options, check [extension configuration
    class](https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Configuration.php#L35-142).

Usage
-----

After installing extension, there would be 4 usage options available for you:

* Subcontexting/extending `Behat\MinkExtension\Context\RawMinkContext` in your feature suite.
  This will give you ability to use preconfigured `Mink` instance altogether with some
  convenience methods:

  - `getSession($name = null)`
  - `assertSession($name = null)`

  `RawMinkContext` doesn't provide any hooks or definitions, so you can inherit from it
  in as many subcontexts as you want - you'll never get `RedundantStepException`.
* Subcontexting/extending `Behat\MinkExtension\Context\MinkContext` in your feature suite.
  Exactly like previous option, but also provides lot of predefined step definitions out
  of the box. As this context provides step definitions and hooks, you can use it **only once**
  inside your feature context tree.
* If you're on the php 5.4+, you can simply use `Behat\MinkExtension\Context\MinkDictionary`
  trait inside your `FeatureContext` or any of its subcontexts. This trait will provide
  all the needed methods, hooks and definitions for you to start. You can use this trait **only
  once** inside your feature context tree.
* Implementing `Behat\MinkExtension\Context\MinkAwareContextInterface` with your context or its
  subcontexts.
  This will give you more customization options. Also, you can use this mechanism on multiple
  contexts avoiding the need to call parent contexts from subcontexts when only thing you need
  is mink instance.

There's common things between those 3 methods. In each of those, target context will implement
`setMink(Mink $mink)` and `setMinkParameters(array $parameters)` methods. Those methods would
be automatically called **immediately after** each context creation before each scenario. And
this `$mink` instance will be preconfigured based on the settings you've provided in your
`behat.yml`.

Concrete `FeatureContext` example:

``` php
<?php

use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext
{
    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
    }
}
```

Dictionary usage example:

``` php
<?php

use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkDictionary;

class FeatureContext extends BehatContext
{
    use MinkDictionary;

    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
    }
}
```

Copyright
---------

Copyright (c) 2012 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Other [awesome developers](https://github.com/Behat/MinkExtension/graphs/contributors)

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
