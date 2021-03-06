<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class ClassActuallyImplementsInterface implements InterfaceWithSomeMethods
{
    public function methodWithReturnType() : bool
    {
        return true;
    }

    public function methodWithTypedParameters(string $parameter)
    {
    }
}
