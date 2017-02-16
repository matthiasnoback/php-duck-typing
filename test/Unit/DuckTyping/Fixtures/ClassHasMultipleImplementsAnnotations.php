<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

/**
 * @implements \Test\Unit\DuckTyping\Fixtures\SomeOtherInterface
 * @implements \Test\Unit\DuckTyping\Fixtures\InterfaceWithSomeMethods
 */
final class ClassHasMultipleImplementsAnnotations
{
}
