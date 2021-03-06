<?php

namespace BonsaiCms\Support\Stubs\Filters;

use Closure;
use Illuminate\Support\Str;

class IndentSpaces
{
    public function __construct(
        protected int $count,
        protected bool $skipFirstLine = true,
        protected string $whiteChar = ' ',
        protected string $lineSeparator = PHP_EOL,
    ) { }

    public function handle($content, Closure $next)
    {
        $content = Str::of($content)
            ->explode($this->lineSeparator)
            ->map(function ($row, $index) {
                return ($row === '' || ($index === 0 && $this->skipFirstLine))
                    ? $row
                    : Str::repeat($this->whiteChar, $this->count) . $row;
            })
            ->join($this->lineSeparator);

        return $next($content);
    }
}
