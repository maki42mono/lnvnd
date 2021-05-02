<?php

namespace library;

class Parser
{
    private Command $command;

    public static function test(): void
    {
        echo "Hello world!";
    }

    public function readCommand(): void
    {
        unset($_SERVER['argv'][0]);
        $console_input = implode(' ', $_SERVER['argv']);
        $pattern = '/^(\w+|\d+|\s|\.|\=|\,|\{|\}|\[|\]|\=])*$/i';
        preg_match($pattern, $console_input, $verified_inputs);
//        Если введены недопустимые символы — сгенерировать ошибку
        if ($console_input !== $verified_inputs[0]) {
            throw new \Exception("Вы ввели недопустимые символы. Можно вводить латиницу, цифры, пробелы и симвлы .={}[]", 500);
        }
        self::parseInput($console_input);
    }

    private function parseInput(string $input): void
    {
        $elements = explode(' ', $input);
//        todo: работать с регистром тут. Если регистр, то можно и в статику вернуться
        $this->command = new Command($elements[0]);
        unset($elements[0]);
        foreach ($elements as $element) {
            self::parseNode($element);
        }
    }

    private function parseNode(string $node)
    {
        $arguments = self::getArguments($node);
        if ($arguments) {
            foreach ($arguments as $argument) {
                $this->command->addArgument($argument);
            }
        } elseif (substr($node, 0, 1) == '[' && substr($node, -1, 1) == ']') {
            $tmp = explode('=', substr($node, 1, mb_strlen($node) - 2));
//            todo: сделать синтаксическую проверку
            $arguments = self::getArguments($tmp[1]);
            if (!$arguments) {
                $arguments = [$tmp[1]];
            }
            $option = new Option($tmp[0], $arguments);
            $this->command->addOption($option);
        }
    }

    private function getArguments(string $raw): bool|array
    {
        if (substr($raw, 0, 1) != '{' || substr($raw, -1, 1) != '}') {
            return false;
        }

        $arguments = substr($raw, 1, mb_strlen($raw) - 2);
        return explode(',', $arguments);
    }

    public function getCommand(): Command
    {
        return $this->command;
    }
}