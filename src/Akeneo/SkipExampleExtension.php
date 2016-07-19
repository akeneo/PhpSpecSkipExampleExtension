<?php

namespace Akeneo;

use Akeneo\Runner;
use PhpSpec\Extension;
use PhpSpec\ServiceContainer;
use PhpSpec\ServiceContainer\IndexedServiceContainer;

class SkipExampleExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(ServiceContainer $container, array $params)
    {
        if (!$container instanceof IndexedServiceContainer) {
            throw new \InvalidArgumentException(sprintf(
                'Container passed from phpspec must implement "%s"!',
                IndexedServiceContainer::class
            ));
        }

        $container->define('runner.maintainers.skip_example', function (IndexedServiceContainer $c) {
            return new Runner\Maintainer\SkipExampleMaintainer();
        }, ['runner.maintainers']);
    }
}
