PhpSpec Skip Example Extension
==============================

This PhpSpec extension allows to skip example through user-friendly annotations.
[![Build Status](https://travis-ci.org/akeneo/PhpSpecSkipExampleExtension.png?branch=master)](https://travis-ci.org/akeneo/PhpSpecSkipExampleExtension)

Installation
------------

Once you have installed PhpSpec (following the documentation on [the official website](http://www.phpspec.net)), add the extension requirement to your `composer.json`:

``` json
{
    "require": {
        "akeneo/phpspec-skip-example-extension": "1.0.*@dev"
    }
}
```

And run composer update:

``` bash
$ php composer.phar update akeneo/phpspec-skip-example-extension
```

Configuration
-------------

You can now activate the extension by creating a `phpspec.yml` file at the root of your project:

``` yaml
extensions:
    - Akeneo\SkipExampleExtension
```

Usage
-----

### @require interface <interface>

Skips all the spec example if the interface is not available

``` php
/**
 * @require interface Vendor\Builder\ToolInterface
 */
class BridgeBuilderSpec extends ObjectBehavior
{
    // Will be skipped if the Vendor\Builder\ToolInterface interface does not exist
    function it_builds_a_brige()
    {
    }

    // Will be skipped if the Vendor\Builder\ToolInterface interface does not exist
    function it_builds_the_road()
    {
    }

    //...
}
```

Contributions
-------------

Feel free to contribute to this extension if you find some interesting ways to improve it!
