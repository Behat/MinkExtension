# MinkExtension

[![Build
Status](https://secure.travis-ci.org/Behat/MinkExtension.png?branch=master)](http://travis-ci.org/Behat/MinkExtension)

Provides integrartion layer:

* Additional services for Behat (`Mink`, `Sessions`, `Drivers`).
* `Behat\MinkExtension\Context\MinkAwareInterface` which provides `Mink`
  instance for your contexts or subcontexts.
* Base `Behat\MinkExtension\Context\MinkContext` context which provides base
  step definitions and hooks for your contexts or subcontexts. Or it could be
  even used as subcontext on its own.

between Behat 2.4+ and Mink 1.4+.

## Installation

This extension requires:

* Behat 2.4+
* Mink 1.4+

### Through PHAR

You could download phars from:

* [Behat downloads](https://github.com/Behat/Behat/downloads)
* [Mink downloads](https://github.com/Behat/Mink/downloads)

After downloading and placing them into project directory, you need to download and
activate `MinkExtension`:

1. [Download extension](https://github.com/downloads/Behat/MinkExtension/mink_extension.phar)
2. Put downloaded phar package into folder with Behat and Mink
3. Tell Behat about extensions with `behat.yml` configuration:

    ``` yaml
    # behat.yml
    default:
      # ...
      extensions:
        mink_extension.phar:
          mink_loader: 'mink-VERSION.phar'
          base_url:    'http://example.com'
          goutte:      ~
          selenium2:   ~
    ```

    For all configuration options, check [extension configuration
    class](https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Extension.php#L91-200).

### Through Composer

1. Set dependencies in your `composer.json`:

    ``` json
    {
        "require": {
            ...

            "behat/mink-extension": "*"
        }
    }
    ```

2. Install/update your vendors:

    ``` bash
    $> curl http://getcomposer.org/installer | php
    $> php composer.phar install
    ```

3. Activate extension in your `behat.yml`:

    ``` yaml
    # behat.yml
    default:
      # ...
      extensions:
        Behat\MinkExtension\Extension:
          base_url:  'http://example.com'
          goutte:    ~
          selenium2: ~
    ```

## Usage

After installing extension, there would be 5 usage options available for you:

1. Writing features with bundled steps only. In this case, you don't need to create
   `boostrap/` folder or custom `FeatureContext` class - Behat will use default
   `MinkContext` by default.
2. Subcontexting/extending `Behat\MinkExtension\Context\RawMinkContext` in your feature suite.
   This will give you ability to use preconfigured `Mink` instance altogether with some
   convenience methods:
   * `getSession($name = null)`
   * `assertSession($name = null)`
   `RawMinkContext` doesn't provide any hooks or definitions, so you can inherit from it
   in as many subcontexts as you want - you'll never get `RedundantStepException`.
3. Subcontexting/extending `Behat\MinkExtension\Context\MinkContext` in your feature suite.
   Exactly like previous option, but also provides lot of predefined step definitions out
   of the box. As this context provides step definitions and hooks, you can use it **only once**
   inside your feature context tree.
4. If you're on the php 5.4+, you can simply use `Behat\MinkExtension\Context\MinkDictionary`
   trait inside your `FeatureContext` or any of its subcontexts. This trait will provide
   all the needed methods, hooks and definitions for you to start. You can use this trait **only
   once** inside your feature context tree.
5. Implementing `Behat\MinkExtension\Context\MinkAwareInterface` with your context or its
   subcontexts.
   This will give you more customization options. Also, you can use this mechanism on multiple
   contexts avoiding the need to call parent contexts from subcontexts when only thing you need
   is mink instance.

There's common things between last 4 methods. In each of those, target context will implement
`setMink(Mink $mink)` and `setMinkParameters(array $parameters)` methods. Those methods would
be automatically called **immediately after** each context creation before each scenario. And
this `$mink` instance will be preconfigured based on the settings you've provided in your
`behat.yml`.

### Context examples

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

## Translated languages

For now exist 10 translated languages: `cs`,`de`,`es`,`fr`,`ja`,`nl`,`pl`,`pt`,`ru`,`sv`.

**Note:** The `ja`,`nl`,`pt` and `sv` are outdated.

#### How to add a new translated language?

If you want to translate another language, you can use as reference the `ru` language file under
[translations folder](https://github.com/Behat/MinkExtension/tree/master/i18n).

**Important:** The filename must match with the same translated language name in [Behat](https://github.com/Behat/Behat/tree/master/i18n) and [Gherkin](https://github.com/Behat/Gherkin/blob/master/i18n.php) in order to work correctly.

If the language does not exist in [Gherkin](https://github.com/Behat/Gherkin/tree/master/i18n).
You should consider making a [Pull Request](https://github.com/cucumber/cucumber/pulls) to
[cucumber\gherkin i18n file](https://github.com/cucumber/gherkin/blob/master/lib/gherkin/i18n.yml).

## Copyright

Copyright (c) 2012 Konstantin Kudryashov (ever.zet). See LICENSE for details.

## Contributors

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Other [awesome developers](https://github.com/Behat/MinkExtension/graphs/contributors)

## Sponsors

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
