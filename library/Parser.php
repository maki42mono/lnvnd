<?php

namespace library;

class Parser
{
    private Command $command;

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
        if ($input == "") {
            echo "Вывести сисок всех команд";
            exit;
        }
        $elements = explode(' ', $input);
        if (count($elements) == 1) {
            throw new \Exception("Проверьте синтаксис", 500);
        }
        $command_name = $elements[0];
        unset($elements[0]);
        if (in_array('{help}', $elements)) {
            if (count($elements) > 1) {
                throw new \Exception("Нелья использовать {help} и задавать значения!", 500);
            }

            echo "HELP";
            exit;
        }

        if (Command::hasOne(['name' => $elements[0]])) {
            throw new \Exception("Команда с таким именем уже зарегистрирована! Задайте другое имя");
        }
        $this->command = new Command($command_name);

        foreach ($elements as $element) {
            self::parseNode($element);
        }
        $this->command->save();
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