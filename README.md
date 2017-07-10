PhpSpec Skip Example Extension
==============================

This PhpSpec extension allows to skip example through user-friendly annotations.
[![Build Status](https://travis-ci.org/akeneo/PhpSpecSkipExampleExtension.png?branch=master)](https://travis-ci.org/akeneo/PhpSpecSkipExampleExtension)

Installation
------------

Once you have installed PhpSpec (following the documentation on [the official website](http://www.phpspec.net)), add the extension requirement to your `composer.json`:

Using phpspec 2.x,

``` json
{
    "require": {
        "akeneo/phpspec-skip-example-extension": "^1.0"
    }
}
```

Using phpspec 3.x,

``` json
{
    "require": {
        "akeneo/phpspec-skip-example-extension": "^2.0"
    }
}
```

Using phpspec 4.x,

``` json
{
    "require": {
        "akeneo/phpspec-skip-example-extension": "^3.0"
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
    Akeneo\SkipExampleExtension: ~
```

Usage
-----

### @require <class or interface>

Skips all the spec example if the class or interface is not available

``` php
/**
 * @require Vendor\Builder\ToolInterface
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
