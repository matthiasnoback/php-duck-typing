<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping;

use function DuckTyping\Object;
use DuckTyping\TypeMismatch;
use PHPUnit\Framework\TestCase;
use Test\Unit\DuckTyping\Fixtures\ClassActuallyImplementsInterface;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethods;
use Test\Unit\DuckTyping\Fixtures\InterfaceWithSomeMethods;

final class ObjectTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_a_convenience_wrapper_for_duck_type_checker()
    {
        $object = new ClassActuallyImplementsInterface();

        Object($object)->shouldBeUsableAs(InterfaceWithSomeMethods::class);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function assert_function_fails_on_type_mismatch()
    {
        $object = new \stdClass();

        $this->expectException(TypeMismatch::class);

        Object($object)->shouldBeUsableAs(InterfaceWithSomeMethods::class);
    }
}
