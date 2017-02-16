<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class ClassWithToString
{
    public function __toString()
    {
        return '';
    }
}
