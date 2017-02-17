<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class ClassWithMatchingMethodsButNoMatchingReturnType
{
    public function methodWithReturnType() : int
    {
        return 1;
    }

    public function methodWithTypedParameters(string $parameter)
    {
    }
}
