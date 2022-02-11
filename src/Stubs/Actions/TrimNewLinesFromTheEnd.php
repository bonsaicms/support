<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;
use Illuminate\Support\Str;

class TrimNewLinesFromTheEnd
{
    public function handle($content, Closure $next)
    {
        $content = Str::of($content)->trim(PHP_EOL);

        return $next($content);
    }
}
