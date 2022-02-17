<?php

namespace BonsaiCms\Support\Stubs\Exceptions;

use Throwable;

class UnknownVariableException extends AbstractException
{
    public function __construct(
        string $variable,
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct("Unknown variable [$variable]", $code, $previous);
    }
}
