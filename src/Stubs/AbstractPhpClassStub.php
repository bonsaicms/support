<?php

namespace BonsaiCms\Support\Stubs;

use BonsaiCms\Support\Stubs\Actions\PostProcessPhpClass;

abstract class AbstractPhpClassStub extends AbstractStub
{
    protected function initializeGenerateActions(): array
    {
        return [
            PostProcessPhpClass::class,
        ];
    }
}
