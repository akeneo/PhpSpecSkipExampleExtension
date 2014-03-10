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

### @skip

Simply skip an example:

``` php
/**
 * @skip it is not runnable
 */
function it_does_unrunnable_stuff()
{
}
```

### @require extension <name>

Skips the example if the extension is not loaded

``` php
/**
 * @require extension mongo
 */
function it_does_mongo_stuff()
{
}
```

### @require php <version constraint>

Skips the example if the current php version does not validate the version constraint

``` php
/**
 * @require php >=5.5
 */
function it_does_yield_stuff()
{
}
```

Contributions
-------------

Feel free to contribute to this extension if you find some interesting ways to improve it!
