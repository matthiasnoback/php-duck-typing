<?php
declare(strict_types = 1);

namespace DuckTyping;

use DuckTyping\Types\HasToStringMethod;

class DuckTypeChecker
{
    public static function valueCanBeUsedAs($type, string $useAsType) : bool
    {
        if (is_object($type)) {
            $type = get_class($type);
        } else {
            $type = gettype($type);
        }

        return self::typeCanBeUsedAs($type, $useAsType);
    }

    private static function typeCanBeUsedAs(string $type, string $useAsType)
    {
        if ($type === $useAsType) {
            return true;
        }

        // TODO this may be the place to allow type coercion

        if (class_exists($type)) {
            if ($useAsType === 'string') {
                // special case to treat objects with __toString() method as a string
                return self::typeCanBeUsedAs($type, HasToStringMethod::class);
            }

            if (is_a($type, $useAsType, true)) {
                return true;
            }

            if (self::hasImplementsAnnotationForInterface($type, $useAsType)) {
                return true;
            }

            return self::hasMatchingMethods($type, $useAsType);
        }

        return false;
    }

    /**
     * Returns `true` if the given object has an annotation claiming that it implements the given interface.
     *
     * @param string $type
     * @param string $interface
     * @return bool
     */
    private static function hasImplementsAnnotationForInterface(string $type, string $interface) : bool
    {
        $docComment = (new \ReflectionClass($type))->getDocComment();
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

    private static function hasMatchingMethods(string $type, string $interface) : bool
    {
        $reflectionClass = new \ReflectionClass($type);

        $reflectionInterface = new \ReflectionClass($interface);
        foreach ($reflectionInterface->getMethods() as $interfaceMethod) {
            if (!$reflectionClass->hasMethod($interfaceMethod->getName())) {
                return false;
            }

            $actual = self::buildMethodSignature($reflectionClass->getMethod($interfaceMethod->getName()));
            $expected = self::buildMethodSignature($interfaceMethod);
            if ($expected['parameters'] != $actual['parameters']) {
                return false;
            }

            if ($expected['return']['type'] !== null) {
                // we expect a specific return type

                if (!$expected['return']['allowsNull'] && $actual['return']['allowsNull']) {
                    // we rely on a return value so the implementation should not allow for null
                    return false;
                }

                if (!self::typeCanBeUsedAs($actual['return']['type'], $expected['return']['type'])) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function buildMethodSignature(\ReflectionMethod $method)
    {
        $signature = [];

        $signature['return'] = [
            'type' => $method->getReturnType() instanceof \ReflectionType ? (string)$method->getReturnType() : null,
            'allowsNull' => $method->getReturnType() ? $method->getReturnType()->allowsNull() : null
        ];

        $signature['parameters'] = [];

        foreach ($method->getParameters() as $parameter) {
            $signature['parameters'][$parameter->getName()] = [
                'type' => (string)$parameter->getType(),
                'allowsNull' => $parameter->getType() ? $parameter->getType()->allowsNull() : null,
            ];
        }

        return $signature;
    }
}
