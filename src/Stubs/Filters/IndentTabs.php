<?php

namespace BonsaiCms\Support\Stubs\Filters;

class IndentTabs extends IndentSpaces
{
    public function __construct(
        protected int $count,
        protected string $whiteChar = '    ',
        protected string $lineSeparator = PHP_EOL,
        protected bool $skipFirstLine = true,
    ) { }
}
