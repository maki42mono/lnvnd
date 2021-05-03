<?php

namespace library;

class Parser
{
    public static function readCommand(): void
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

    private static function parseInput(string $input): void
    {
        if ($input == "") {
            $commands = Command::findAll();
            if ($commands == null) {
                throw new \Exception("Не зарегистрировано ни одной команды! Зарегистрируйте что-то, потом выводите", 500);
            }

            echo "====Список команд====\n\n";
            foreach ($commands as $command) {
                echo "{$command}\n";
                echo "====\n\n";
            }
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
        $command = new Command($command_name, $elements);
        $command->save();
    }
}