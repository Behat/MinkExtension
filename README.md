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
          mink_loader:        mink-VERSION.phar
          base_url:           http://example.com

          javascript_session: selenium2

          goutte:             ~
          selenium2:          ~
    ```

    For all configuration options, check [extension configuration
    class](https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Configuration.php#L35-142).

Usage
-----

After installing extension, there would be 3 usage options available for you:

* Subcontexting/extending `Behat\MinkExtension\Context\RawMinkContext` in your feature suite.
  This will give you ability to use preconfigured `Mink` instance altogether with some
  convenience methods:

  - `getSession($name = null)`
  - `assertSession($name = null)`

  And autohook, which will switch current session based on scenario tags.
* Subcontexting/extending `Behat\MinkExtension\Context\MinkContext` in your feature suite.
  Exactly like previous option, but also provides lot of predefined step definitions out
  of the box.
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

Copyright
---------

Copyright (c) 2012 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Pascal Cremer [b00giZm](http://github.com/b00giZm) [ZombieDriver creator]
* Alexandre Salomé [alexandresalome](http://github.com/alexandresalome) [SeleniumDriver creator]
* Pete Otaqui [pete-otaqui](http://github.com/pete-otaqui) [Selenium2Driver creator]

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
