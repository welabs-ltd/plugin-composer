<?php

declare(strict_types=1);

namespace WeLabs\PluginStub\ThirdParty\Packages\League\Container\Argument;

use WeLabs\PluginStub\ThirdParty\Packages\League\Container\ContainerAwareInterface;
use ReflectionFunctionAbstract;

interface ArgumentResolverInterface extends ContainerAwareInterface
{
    public function resolveArguments(array $arguments): array;
    public function reflectArguments(ReflectionFunctionAbstract $method, array $args = []): array;
}
