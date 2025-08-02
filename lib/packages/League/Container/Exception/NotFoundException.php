<?php

declare(strict_types=1);

namespace WeLabs\PluginComposer\ThirdParty\Packages\League\Container\Exception;

use WeLabs\PluginComposer\ThirdParty\Packages\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
