<?php


namespace library;


class Command extends DomainObject
{
    private string $name;
    private array $arguments = [];
    private array $options = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        return parent::__construct();
    }

    public function addArgument(string $argument): void
    {
        $this->arguments[] = $argument;
    }

    /*public function getArguments(): array
    {
        return $this->arguments;
    }*/

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    /*public function getOptions(): array
    {
        return $this->options;
    }*/

    /*public function getName(): string
    {
        return $this->name;
    }*/

    public function __toString(): string
    {
        $res = "\nНазвание команды: {$this->name}\n";

        if ($this->arguments != []) {
            $tmp_str = "";
            foreach ($this->arguments as $argument) {
                $tmp_str .= "    — {$argument}\n";
            }

            $res .= "\nАргументы:\n{$tmp_str}";
        }

        if ($this->options == []) {
            return $res;
        }

        $tmp_str = "";
        foreach ($this->options as $option) {
            $tmp_str .= $option;
        }

        $res .= "\nОпции:\n{$tmp_str}";

        return $res;
    }

    protected static function targetMapper(): Mapper
    {
        return new CommandMapper();
    }

    protected function beforeSave()
    {
        $this->attributes["name"] = $this->name;
        $this->attributes["arguments"] = implode(';', $this->arguments);

        $get_options = function (array $options) {
            $res = "";
            foreach ($options as $option) {
                $arguments = $option->getValues();
                $res .= "{$option->getName()}=" . implode(',', $arguments) . ";";
            }
            return substr($res, 0, mb_strlen($res) - 1);
        };
        $this->attributes["options"] = $get_options($this->options);
    }

    public static function findOne(array $raw): DomainObject|null
    {
        $mapper = self::targetMapper();
        return $mapper->findOneByMapper($raw);
    }
}