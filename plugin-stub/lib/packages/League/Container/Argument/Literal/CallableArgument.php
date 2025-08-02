<?php

declare(strict_types=1);

namespace BaseNameSpace\PluginStub\ThirdParty\Packages\League\Container\Argument\Literal;

use BaseNameSpace\PluginStub\ThirdParty\Packages\League\Container\Argument\LiteralArgument;

class CallableArgument extends LiteralArgument
{
    public function __construct(callable $value)
    {
        parent::__construct($value, LiteralArgument::TYPE_CALLABLE);
    }
}
