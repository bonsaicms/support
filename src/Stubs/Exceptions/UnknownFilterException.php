<?php

namespace BonsaiCms\Support\Stubs\Exceptions;

use Throwable;

class UnknownFilterException extends AbstractException
{
    public function __construct(
        string $filter,
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct("Unknown filter [$filter]", $code, $previous);
    }
}
