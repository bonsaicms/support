<?php

namespace BonsaiCms\Support\Stubs;

use BonsaiCms\Support\Stubs\Actions\PostProcessPhpClass;
use BonsaiCms\Support\Stubs\Actions\SkipWhiteCharsBeforeSemicolons;

abstract class AbstractPhpClassStub extends AbstractStub
{
    protected function initializeGenerateActions(): array
    {
        return [
            PostProcessPhpClass::class,
            SkipWhiteCharsBeforeSemicolons::class,
        ];
    }
}
