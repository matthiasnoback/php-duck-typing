<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping;

use DuckTyping\DuckTypeChecker;
use DuckTyping\TypeMismatch;
use PHPUnit\Framework\TestCase;
use Test\Unit\DuckTyping\Fixtures\ClassActuallyImplementsInterface;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethods;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethodsButNoMatchingParameters;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethodsButNoMatchingReturnType;
use Test\Unit\DuckTyping\Fixtures\Entity;
use Test\Unit\DuckTyping\Fixtures\InterfaceWithSomeMethods;
use Test\Unit\DuckTyping\Fixtures\ToStringId;

class DuckTypeCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function a_class_that_actually_implements_a_given_interface_can_be_used_as_an_implementation_for_that_interface()
    {
        $object = new ClassActuallyImplementsInterface();
        $this->expectSuccess($object, InterfaceWithSomeMethods::class);
    }

    /**
     * @test
     */
    public function a_class_with_matching_methods_can_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethods();
        $this->expectSuccess($object, InterfaceWithSomeMethods::class);
    }

    /**
     * @test
     */
    public function a_class_with_non_matching_method_parameters_can_not_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethodsButNoMatchingParameters();

        $this->expectFailureWithReasons($object, InterfaceWithSomeMethods::class, ['Parameter list of "methodWithTypedParameters()" does not match']);
    }

    /**
     * @test
     */
    public function a_class_with_non_matching_method_return_type_can_not_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethodsButNoMatchingReturnType();
        $this->expectFailureWithReasons($object, InterfaceWithSomeMethods::class, [
            'Return type of "methodWithReturnType()" does not match'
        ]);
    }

    /**
     * @test
     */
    public function a_method_which_returns_a_to_string_object_can_be_used_as_a_method_that_returns_a_string()
    {
        $object = new class
        {
            public function id(): ToStringId
            {
                return new ToStringId();
            }
        };

        $this->expectSuccess($object, Entity::class);
    }

    /**
     * @test
     */
    public function a_string_can_be_used_as_a_string()
    {
        $this->expectSuccess('Some string', 'string');
    }

    /**
     * @test
     */
    public function an_object_with_a_to_string_method_can_be_used_as_a_string()
    {
        $object = new class()
        {
            public function __toString()
            {
                return '';
            }
        };

        $this->expectSuccess($object, 'string');
    }

    /**
     * @test
     */
    public function a_method_with_a_nullable_return_type_can_not_be_used_as_a_method_with_a_non_nullable_return_type()
    {
        $object = new class()
        {
            public function id(): ?string
            {
                return '';
            }
        };

        $expected = new class()
        {
            public function id(): string
            {
                return '';
            }
        };

        $this->expectFailureWithReasons($object, get_class($expected), [
            'Return type of "id()" must not be nullable'
        ]);
    }

    private function expectFailureWithReasons($object, string $useAsType, array $reasons)
    {
        $expectedException = new TypeMismatch(get_class($object), $useAsType, $reasons);

        try {
            DuckTypeChecker::valueCanBeUsedAs($object, $useAsType);
            $this->fail('Expected a failure');
        } catch (TypeMismatch $actualException) {
            $this->assertEquals($expectedException, $actualException);
        }
    }

    private function expectSuccess($object, $class)
    {
        DuckTypeChecker::valueCanBeUsedAs($object, $class);

        $this->assertTrue(true);
    }
}
