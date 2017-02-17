<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

use DuckTyping\Types\HasToStringMethod;

interface Entity
{
    public function id() : HasToStringMethod;
}
