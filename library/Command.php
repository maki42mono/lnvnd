<?php


namespace library;


class Command extends DomainObject
{
    private string $name;
    private array $arguments = [];
    private array $options = [];

    public function __construct(string $name, array $elements = [])
    {
        $this->name = $name;
        foreach ($elements as $element) {
            if ($element == null) {
                continue;
            }
            $this->parseNode($element);
        }
        return parent::__construct();
    }

    private function parseNode(string $node)
    {
        $arguments = self::parseArguments($node);
        if ($arguments) {
            foreach ($arguments as $argument) {
                $this->addArgument($argument);
            }
        } elseif (substr($node, 0, 1) == '[' && substr($node, -1, 1) == ']') {
            $tmp = explode('=', substr($node, 1, mb_strlen($node) - 2));
//            todo: сделать синтаксическую проверку
            $arguments = self::parseArguments($tmp[1]);
            if (!$arguments) {
                $arguments = [$tmp[1]];
            }
            $option = new Option($tmp[0], $arguments);
            $this->addOption($option);
        }
    }

    private function parseArguments(string $raw): bool|array
    {
        if (substr($raw, 0, 1) != '{' || substr($raw, -1, 1) != '}') {
            return false;
        }

        $arguments = substr($raw, 1, mb_strlen($raw) - 2);
        return explode(',', $arguments);
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
        if ($this->attributes["arguments"] != []) {
            $this->attributes["arguments"] = '{' . implode(',', $this->arguments) . '}';
        }

        $get_options = function (array $options) {
            $res = "";
            foreach ($options as $option) {
                $arguments = $option->getValues();
                $res .= "[{$option->getName()}={" . implode(',', $arguments) . "}];";
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

    public static function findAll(): array|null
    {
        $mapper = self::targetMapper();
        return $mapper->findAll();
    }

    public static function hasOne(array $raw): bool
    {
        $mapper = self::targetMapper();
        return $mapper->hasOneByMapper($raw);
    }
}