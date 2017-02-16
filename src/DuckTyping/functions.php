<?php
declare(strict_types = 1);

namespace DuckTyping;

function Object($object)
{
    return new ObjectWrapper($object);
}
