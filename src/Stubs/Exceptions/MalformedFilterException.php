<?php

namespace BonsaiCms\Support\Stubs\Exceptions;

use Throwable;

class MalformedFilterException extends AbstractException
{
    public function __construct(
        string $filter,
        int $code = 0,
        ?Throwable $previous = null
    )
    {
        parent::__construct("Malformed filter [$filter]", $code, $previous);
    }
}
