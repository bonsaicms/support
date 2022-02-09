<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;

class ReplaceVariables
{
    public function __construct(
        protected array $variables
    ) {}

    public function handle($content, Closure $next)
    {
        $names = [];
        $values = [];

        foreach ($this->variables as $name => $value) {
            $names[] = '{{'.$name.'}}';
            $values[] = $value;
            $names[] = '{{ '.$name.' }}';
            $values[] = $value;
        }

        $content = str_replace($names, $values, $content);

        return $next($content);
    }
}
