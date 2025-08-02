<?php

declare(strict_types=1);

namespace WeLabs\PluginStub\ThirdParty\Packages\League\Container\Argument\Literal;

use WeLabs\PluginStub\ThirdParty\Packages\League\Container\Argument\LiteralArgument;

class IntegerArgument extends LiteralArgument
{
    public function __construct(int $value)
    {
        parent::__construct($value, LiteralArgument::TYPE_INT);
    }
}
