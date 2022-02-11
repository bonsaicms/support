<?php

namespace BonsaiCms\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PhpDependenciesCollection extends Collection
{
    public function toPhpUsesString(string $namespace): string
    {
        return $this
            ->unique()
            ->when($namespace, function ($collection) use ($namespace) {
                return $collection->reject(fn ($use) => Str::startsWith($use,
                    Str::finish($namespace, '\\')
                ));
            })
            ->sort()
            ->sortBy(fn ($use) => strlen($use))
            ->map(fn ($use) => "use $use;")
            ->join(PHP_EOL);
    }
}
