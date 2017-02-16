<?php
declare(strict_types = 1);

namespace DuckTyping;

final class ObjectWrapper
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function canBeUsedAs(string $interface) : bool
    {
        return DuckTypeChecker::valueCanBeUsedAs($this->object, $interface);
    }

    public function shouldBeUsableAs(string $interface)
    {
        if (!$this->canBeUsedAs($interface)) {
            throw new TypeMismatch($this->object, $interface, []);
        }
    }
}
