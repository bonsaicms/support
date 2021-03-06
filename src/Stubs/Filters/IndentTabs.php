<?php

namespace BonsaiCms\Support\Stubs\Filters;

class IndentTabs extends IndentSpaces
{
    public function __construct(
        protected int $count,
        protected bool $skipFirstLine = true,
        protected string $whiteChar = '    ',
        protected string $lineSeparator = PHP_EOL,
    ) { }
}
