<?php
declare(strict_types = 1);

namespace Test\Unit\DuckTyping\Fixtures;

interface Entity
{
    public function id() : string;
}
