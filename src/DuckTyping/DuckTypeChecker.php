<?php
declare(strict_types = 1);

namespace DuckTyping;

use Assert\Assertion;

class DuckTypeChecker
{
    public static function valueCanBeUsedAs($object, string $interface) : bool
    {
        Assertion::isObject($object);
        Assertion::interfaceExists($interface);

        if ($object instanceof $interface) {
            return true;
        }

        if (self::hasImplementsAnnotationForInterface($object, $interface)) {
            return true;
        }

        return self::hasMatchingMethods($object, $interface);
    }

    /**
     * Returns `true` if the given object has an annotation claiming that it implements the given interface.
     *
     * @param object $object
     * @param string $interface
     * @return bool
     */
    private static function hasImplementsAnnotationForInterface($object, string $interface) : bool
    {
        $docComment = (new \ReflectionObject($object))->getDocComment();
        if (!$docComment) {
            return false;
        }

        if (preg_match_all('/^ \* \@implements (.+)$/m', $docComment, $matches) === 0) {
            return false;
        }

        $implementsTags = $matches[1];

        foreach ($implementsTags as $tag) {
            if (is_a((string)$tag, $interface, true)) {
                return true;
            }
        }

        return false;
    }

    private static function hasMatchingMethods($object, string $interface) : bool
    {
        $reflectionObject = new \ReflectionObject($object);

        $reflectionInterface = new \ReflectionClass($interface);
        foreach ($reflectionInterface->getMethods() as $interfaceMethod) {
            if (!$reflectionObject->hasMethod($interfaceMethod->getName())) {
                return false;
            }

            $actual = self::buildMethodSignature($reflectionObject->getMethod($interfaceMethod->getName()));
            $expected = self::buildMethodSignature($interfaceMethod);
            if ($expected != $actual) {
                return false;
            }
        }

        return true;
    }

    private static function buildMethodSignature(\ReflectionMethod $method)
    {
        $signature = [];

        $signature['return_type'] = [
            'type' => (string)$method->getReturnType(),
            'allowsNull' => $method->getReturnType() ? $method->getReturnType()->allowsNull() : null
        ];

        foreach ($method->getParameters() as $parameter) {
            $signature['parameters'][$parameter->getName()] = [
                'type' => (string)$parameter->getType(),
                'allowsNull' => $parameter->getType() ? $parameter->getType()->allowsNull() : null,
            ];
        }

        return $signature;
    }
}
