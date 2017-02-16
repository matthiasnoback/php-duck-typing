<?php
declare(strict_types = 1);

namespace DuckTyping;

final class TypeMismatch extends \LogicException
{
    public function __construct($object, string $interface, array $reasons)
    {
        parent::__construct(
            sprintf(
                "Object of type \"%s\" can not be used as an instance of \"%s\": \n",
                get_class($object),
                implode("\n", array_map(function (\Exception $exception) {
                    return sprintf(
                        "- %s\n",
                        $exception->getMessage()
                    );
                }, $reasons)),
                $interface
            )
        );
    }
}
