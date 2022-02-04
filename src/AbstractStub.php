<?php

namespace BonsaiCms\Support;

use Illuminate\Support\Facades\File;

abstract class AbstractStub
{
    protected string $stubContent;
    protected bool $stubContentLoaded = false;

    public function __construct(
        protected string $stubFileName,
        protected array $variables = []
    ) {}

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

    protected function replaceVariables(string $content, array $variables): string
    {
        $names = [];
        $values = [];

        foreach ($variables as $name => $value) {
            $names[] = '{{'.$name.'}}';
            $values[] = $value;
            $names[] = '{{ '.$name.' }}';
            $values[] = $value;
        }

        return str_replace($names, $values, $content);
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

    public function generate(): string
    {
        return $this->replaceVariables(
            $this->getStubContent(),
            $this->variables
        );
    }

    public function __toString(): string
    {
        return $this->generate();
    }

    public static function make(string $stubFileName, array $variables = []): string
    {
        return (new static($stubFileName, $variables))->generate();
    }

    /*
     * Generate to file
     */

    public function writeToFile(string $filePath, bool $lock = false): int|bool
    {
        return File::put(
            $filePath,
            $this->generate()
        );
    }

    public static function write(string $stubFileName, string $filePath, array $variables = [], bool $lock = false): int|bool
    {
        return (new static($stubFileName, $variables))->writeToFile($filePath, $lock);
    }

    /*
     * Stub File Paths
     */

    abstract protected function resolveDefaultStubFilePath(string $stubFileName): string;

    abstract protected function resolveOverriddenStubFilePath(string $stubFileName): string|null;
}
