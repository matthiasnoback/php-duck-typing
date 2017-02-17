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

    public function shouldBeUsableAs(string $interface)
    {
        DuckTypeChecker::valueCanBeUsedAs($this->object, $interface);
    }
}
