<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;

class SqueezeTheSameLines
{
    public function __construct(
        protected string $line
    ) {}

    public function handle($content, Closure $next)
    {
        do {
            $replaced = 0;
            $content = str_replace([
                $this->line.PHP_EOL.$this->line,
            ], [
                $this->line
            ], $content, $replaced);
        } while ($replaced > 0);

        return $next($content);
    }
}
