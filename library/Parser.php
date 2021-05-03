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

            if (!Command::hasOne(['name' => $command_name])) {
                throw new \Exception("Команда {$command_name} не зарегистрирована", 500);
            }

            $command = Command::findOne(['name' => $command_name]);

            echo $command;
            exit;
        }

        if (Command::hasOne(['name' => $command_name])) {
            throw new \Exception("Команда с таким именем уже зарегистрирована! Задайте другое имя");
        }
        $this->command = new Command($command_name, $elements);
        $this->command->save();
    }

    public function getCommand(): Command
    {
        return $this->command;
    }
}