<?php

namespace BonsaiCms\Support\Stubs;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\File;
use BonsaiCms\Support\Stubs\Filters\IndentTabs;
use BonsaiCms\Support\Stubs\Filters\IndentSpaces;
use BonsaiCms\Support\Stubs\Actions\ReplaceVariables;

abstract class AbstractStub
{
    protected string $stubContent;
    protected bool $stubContentLoaded = false;
    protected array $generateActions;
    protected array $filters;

    public function __construct(
        protected string $stubFileName,
        protected array $variables = [],
        array $appendGenerateActions = []
    ) {
        $this->generateActions = $this->initializeGenerateActions();
        $this->appendGenerateActions($appendGenerateActions);
        $this->initializeFilters();
    }

    public function initializeFilters(): void
    {
        $this->filters = [
            'indentSpaces' => IndentSpaces::class
        ];
    }

    public function getStubSuffix(): string
    {
        return '.stub';
    }

    public function getStubFileName(): string
    {
        return $this->stubFileName . $this->getStubSuffix();
    }

    public function getStubContent(bool $recache = false): string
    {
        if ($this->stubContentLoaded !== true || $recache) {
            $this->stubContentLoaded = true;
            $this->stubContent = $this->readStubFileContent();
        }

        return $this->stubContent;
    }

    protected function readStubFileContent(): string
    {
        $stubFileName = $this->getStubFileName();
        $overriddenStubFilePath = $this->resolveOverriddenStubFilePath($stubFileName);

        return File::get(
            $overriddenStubFilePath && File::exists($overriddenStubFilePath)
                ? $overriddenStubFilePath
                : $this->resolveDefaultStubFilePath($stubFileName)
        );
    }

    /*
     * Variables
     */

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    public function setVariable(string $variable, string $value): self
    {
        $this->variables[$variable] = $value;

        return $this;
    }

    public function getVariable(string $variable): string|null
    {
        return $this->variables[$variable] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->setVariable($name, $value);
    }

    public function __get(string $name)
    {
        return $this->getVariable($name);
    }

    /*
     * Generate
     */

    public function generate(?array $filters = null): string
    {
        return app(Pipeline::class)
            ->send($this->getStubContent())
            ->through([
                app(ReplaceVariables::class, [
                    'variables' => $this->variables,
                    'filters' => $filters ?? $this->getDefaultFilters()
                ]),
                ...$this->getGenerateActions(),
            ])
            ->thenReturn();
    }

    public function __toString(): string
    {
        return $this->generate();
    }

    public static function make(string $stubFileName, array $variables = [], array $appendGenerateActions = [], ?array $filters = null): string
    {
        return (new static($stubFileName, $variables, $appendGenerateActions))->generate($filters);
    }

    public function getDefaultFilters(): array
    {
        return [
            'indentTabs' => IndentTabs::class,
            'indentSpaces' => IndentSpaces::class,
        ];
    }

    /*
     * Generate Actions
     */

    protected function initializeGenerateActions(): array
    {
        return [];
    }

    public function getGenerateActions(): array
    {
        return $this->generateActions;
    }

    public function setGenerateActions(array $generateActions): self
    {
        $this->generateActions = $generateActions;

        return $this;
    }

    public function appendGenerateActions(array $generateActions): self
    {
        $this->generateActions = [
            ...$this->generateActions,
            ...$generateActions
        ];

        return $this;
    }

    /*
     * Generate to file
     */

    public function writeToFile(string $filePath, bool $lock = false, ?array $filters = null): int|bool
    {
        return File::put(
            $filePath,
            $this->generate($filters)
        );
    }

    public static function write(string $stubFileName, string $filePath, array $variables = [], bool $lock = false, ?array $filters = null): int|bool
    {
        return (new static($stubFileName, $variables))->writeToFile($filePath, $lock, $filters);
    }

    /*
     * Stub File Paths
     */

    abstract protected function resolveDefaultStubFilePath(string $stubFileName): string;

    abstract protected function resolveOverriddenStubFilePath(string $stubFileName): string|null;
}
