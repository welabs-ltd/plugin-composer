<?php

declare(strict_types=1);

namespace WeLabs\PluginComposer\ThirdParty\Packages\League\Container\Inflector;

use IteratorAggregate;
use WeLabs\PluginComposer\ThirdParty\Packages\League\Container\ContainerAwareInterface;

interface InflectorAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    public function add(string $type, callable $callback = null): Inflector;
    public function inflect(object $object);
}
