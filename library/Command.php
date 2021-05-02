<?php


namespace library;


class Command
{
    private string $name;
    private array $arguments = [];
    private array $options = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addArgument(string $argument): void
    {
        $this->arguments[] = $argument;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getName(): string
    {
        return $this->name;
    }
}