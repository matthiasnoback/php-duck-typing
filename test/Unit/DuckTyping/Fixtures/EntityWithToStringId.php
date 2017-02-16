<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

final class EntityWithToStringId
{
    public function id() : ToStringId
    {
    }
}
