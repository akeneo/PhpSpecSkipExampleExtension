<?php

namespace Akeneo;

use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;
use Akeneo\Runner;

class SkipExampleExtension implements ExtensionInterface
{
    public function load(ServiceContainer $container)
    {
        $container->set('runner.maintainers.skip_example', function ($c) {
            return new Runner\Maintainer\SkipExampleMaintainer();
        });
    }
}
