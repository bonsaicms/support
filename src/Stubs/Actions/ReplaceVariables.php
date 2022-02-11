<?php

namespace BonsaiCms\Support\Stubs\Actions;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Pipeline\Pipeline;
use BonsaiCms\Support\Stubs\Exceptions\UnknownFilterException;
use BonsaiCms\Support\Stubs\Exceptions\MalformedFilterException;

class ReplaceVariables
{
    public function __construct(
        protected array $variables,
        protected array $filters = []
    ) {}

    protected function formatVariableValue($value, $formatter)
    {
        return app(Pipeline::class)
            ->send($value)
            ->through(
                Str::of($formatter)
                    ->explode('|')
                    ->reject(static fn ($filter) => $filter === '')
                    ->map(function ($filter) {
                        $matches = [];
                        $matched = preg_match(
                            '/^([A-Za-z0-9_]+)(?:\((.*)\))?$/',
                            $filter,
                            $matches
                        );

                        if ($matched !== 1) {
                            throw new MalformedFilterException($filter);
                        }

                        $functionName = $matches[1];

                        $arguments = (isset($matches[2]))
                            ? eval('return ['.$matches[2].'];')
                            : [];

                        if (! isset($this->filters[$functionName])) {
                            throw new UnknownFilterException($functionName);
                        }

                        $filterCallable = $this->filters[$functionName];

                        return (is_callable($filterCallable))
                            ? static fn ($value) => $filterCallable($value, ...$arguments)
                            : new $filterCallable(...$arguments);
                })
                ->filter(static fn ($callable) => $callable !== null)
                ->toArray()
            )
            ->thenReturn();
    }

    public function handle($content, Closure $next)
    {
        $content = preg_replace_callback(
            '/\{\{ *([A-Za-z0-9_]+)(|.*?)? *\}\}/',
            function ($matches) {
                [ $whole, $variable, $formatter ] = $matches;
                return $this->formatVariableValue(
                    $this->variables[$variable],
                    $formatter
                );
            },
            $content
        );

        return $next($content);
    }
}
