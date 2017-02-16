<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class ToStringId
{
    public function __toString() : string
    {
        return '';
    }
}
