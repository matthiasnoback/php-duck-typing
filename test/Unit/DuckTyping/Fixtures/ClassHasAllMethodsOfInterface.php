<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

/**
 * @implements \Test\Unit\DuckTyping\Fixtures\InterfaceWithSomeMethods
 */
final class ClassHasAllMethodsOfInterface
{
    public function methodWithReturnType() : bool
    {
    }

    public function methodWithTypedParameters(string $parameter)
    {
    }
}
