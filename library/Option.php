<?php


namespace library;


class Option
{
    private string $name;
    private array $values = [];

    public function __construct(string $name, array $values = [])
    {
        $this->name = $name;
        if (isset($values)) {
            foreach ($values as $value) {
                $this->values[] = (string)$value;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        $res = "    — {$this->name}\n";

        $values_str = "";
        foreach ($this->values as $value) {
            $values_str .= "        — {$value}\n";
        }

        return $res . $values_str;
    }
}