<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;

class SkipEmptyLines
{
    public function handle($content, Closure $next)
    {
        do {
            $replaced = 0;
            $content = str_replace([
                PHP_EOL.PHP_EOL,
            ], [
                PHP_EOL
            ], $content, $replaced);
        } while ($replaced > 0);

        return $next($content);
    }
}
