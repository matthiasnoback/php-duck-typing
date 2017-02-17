<?php
declare(strict_types = 1);

namespace DuckTyping;

use DuckTyping\Types\HasToStringMethod;

class DuckTypeChecker
{
    public static function valueCanBeUsedAs($value, string $useAsType) : void
    {
        if (is_object($value)) {
            $type = get_class($value);
        } else {
            $type = gettype($value);
        }

        $reasons = [];
        $result = self::typeCanBeUsedAs($type, $useAsType, $reasons);

        if ($result) {
            return;
        }

        throw new TypeMismatch($type, $useAsType, $reasons);
    }

    private static function typeCanBeUsedAs(string $type, string $useAsType, array &$reasons)
    {
        if ($type === $useAsType) {
            return true;
        }

        // TODO this may be the place to allow type coercion

        if (class_exists($type)) {
            if ($useAsType === 'string') {
                // special case to treat objects with __toString() method as a string
                return self::typeCanBeUsedAs($type, HasToStringMethod::class, $reasons);
            }

            if (is_a($type, $useAsType, true)) {
                return true;
            }

            return self::hasMatchingMethods($type, $useAsType, $reasons);
        }

        return false;
    }

    private static function hasMatchingMethods(string $type, string $interface, array &$reasons) : bool
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
                $reasons[] = sprintf('Parameter list of "%s()" does not match', $interfaceMethod->getName());
                return false;
            }

            if ($expected['return']['type'] !== null) {
                // we expect a specific return type

                if (!$expected['return']['allowsNull'] && $actual['return']['allowsNull']) {
                    // we rely on a return value so the implementation should not allow for null
                    $reasons[] = sprintf('Return type of "%s()" must not be nullable', $interfaceMethod->getName());
                    return false;
                }

                if (!self::typeCanBeUsedAs($actual['return']['type'], $expected['return']['type'], $reasons)) {
                    $reasons[] = sprintf('Return type of "%s()" does not match', $interfaceMethod->getName());

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
