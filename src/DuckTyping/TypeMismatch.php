<?php
declare(strict_types = 1);

namespace DuckTyping;

final class TypeMismatch extends \LogicException
{
    public function __construct(string $type, string $useAsType, array $reasons)
    {
        parent::__construct(
            sprintf(
                "Type \"%s\" can not be used as an instance of \"%s\": \n%s",
                $type,
                $useAsType,
                implode("\n", array_map(function (string $reason) {
                    return sprintf(
                        "- %s",
                        $reason
                    );
                }, $reasons))
            )
        );
    }
}
