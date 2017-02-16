<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping;

use DuckTyping\DuckTypeChecker;
use PHPUnit\Framework\TestCase;
use Test\Unit\DuckTyping\Fixtures\ClassActuallyImplementsInterface;
use Test\Unit\DuckTyping\Fixtures\ClassHasAllMethodsOfInterface;
use Test\Unit\DuckTyping\Fixtures\ClassHasMultipleImplementsAnnotations;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethods;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethodsButNoMatchingParameters;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethodsButNoMatchingParameterType;
use Test\Unit\DuckTyping\Fixtures\ClassWithMatchingMethodsButNoMatchingReturnType;
use Test\Unit\DuckTyping\Fixtures\ClassWithNoDocComment;
use Test\Unit\DuckTyping\Fixtures\ClassWithNoImplementsAnnotations;
use Test\Unit\DuckTyping\Fixtures\ClassWithNonMatchingImplementsAnnotations;
use Test\Unit\DuckTyping\Fixtures\InterfaceWithSomeMethods;

class DuckTypeCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function a_class_that_actually_implements_a_given_interface_can_be_used_as_an_implementation_for_that_interface()
    {
        $object = new ClassActuallyImplementsInterface();
        $this->assertTrue(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_an_implements_annotation_can_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassHasAllMethodsOfInterface();
        $this->assertTrue(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_matching_methods_can_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethods();
        $this->assertTrue(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_non_matching_method_parameters_can_not_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethodsButNoMatchingParameters();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_non_matching_method_return_type_can_not_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethodsButNoMatchingReturnType();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_non_matching_method_parameter_type_can_not_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassWithMatchingMethodsButNoMatchingParameterType();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_multiple_implements_annotations_can_be_used_as_an_implementation_for_the_given_interface()
    {
        $object = new ClassHasMultipleImplementsAnnotations();
        $this->assertTrue(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_no_implements_annotations_can_not_be_used()
    {
        $object = new ClassWithNoImplementsAnnotations();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_no_matching_implements_annotations_can_not_be_used()
    {
        $object = new ClassWithNonMatchingImplementsAnnotations();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }

    /**
     * @test
     */
    public function a_class_with_no_doc_comment_can_not_be_used()
    {
        $object = new ClassWithNoDocComment();
        $this->assertFalse(DuckTypeChecker::valueCanBeUsedAs($object, InterfaceWithSomeMethods::class));
    }
}
