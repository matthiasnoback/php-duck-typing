<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

interface InterfaceWithSomeMethods
{
    public function methodWithReturnType() : bool;

    public function methodWithTypedParameters(string $parameter);
}
