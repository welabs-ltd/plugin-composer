<?php

declare(strict_types=1);

namespace BaseNameSpace\PluginStub\ThirdParty\Packages\League\Container\Exception;

use BaseNameSpace\PluginStub\ThirdParty\Packages\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
