<?php

declare(strict_types=1);

namespace BaseNameSpace\PluginStub\ThirdParty\Packages\League\Container\ServiceProvider;

use BaseNameSpace\PluginStub\ThirdParty\Packages\League\Container\ContainerAwareInterface;

interface ServiceProviderInterface extends ContainerAwareInterface
{
    public function getIdentifier(): string;
    public function provides(string $id): bool;
    public function register(): void;
    public function setIdentifier(string $id): ServiceProviderInterface;
}
