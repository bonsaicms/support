<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;

class SkipWhiteCharsBeforeSemicolons
{
    public function handle($content, Closure $next)
    {
        $content = preg_replace(
            '/[[:blank:]\n]+;/',
            ';',
            $content
        );

        return $next($content);
    }
}
