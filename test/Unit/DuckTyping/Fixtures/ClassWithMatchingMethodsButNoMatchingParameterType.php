<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class ClassWithMatchingMethodsButNoMatchingParameterType
{
    public function methodWithReturnType() : bool
    {
    }

    public function methodWithTypedParameters(int $parameter)
    {
    }
}
