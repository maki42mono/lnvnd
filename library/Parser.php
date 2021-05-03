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
            throw new \Exception("Вы ввели недопустимые символы. Можно вводить латиницу, цифры, пробелы и симвлы .={}[]",
                500);
        }
        self::parseInput($console_input);
    }

    private static function parseInput(string $input): void
    {
//        Пытаемся получить список всех команд
        if ($input == "") {
            $commands = Command::findAll();
//            Если не зарегистрировано ни одной команды — показать ошибку
            if ($commands == null) {
                throw new \Exception("Не зарегистрировано ни одной команды! Зарегистрируйте что-то, потом выводите",
                    500);
            }

//            Иначе вывести список всех команд
            echo "====Список команд====\n\n";
            foreach ($commands as $command) {
                echo "{$command}\n";
                echo "====\n\n";
            }
            exit;
        }

//        Читаем все параметры скрипта через пробелы
        $elements = explode(' ', $input);
//        Если скрипт вызывается с одним параметром — вернуть ошибку
        if (count($elements) == 1) {
            throw new \Exception("Проверьте синтаксис", 500);
        }

//        todo: добавить проверку названия команды
        $command_name = $elements[0];
        unset($elements[0]);
//        Указали {help} в параметрах
        if (in_array('{help}', $elements)) {
//            Если попимо {help} и названия скрипта есть еще что-то — вернуть ошибку
            if (count($elements) > 1) {
                throw new \Exception("Нелья использовать {help} и задавать значения!", 500);
            }
//            Если пытаемся вывести инфу о несуществующей команде — вернуть ошибку
            if (!Command::hasOne(['name' => $command_name])) {
                throw new \Exception("Команда {$command_name} не зарегистрирована", 500);
            }

//            Иначе выводим инфу о команде
            $command = Command::findOne(['name' => $command_name]);

            echo $command;
            exit;
        }

//        Если пытаемся зарегистрировать существующею команду — вернуть ошибку
        if (Command::hasOne(['name' => $command_name])) {
            throw new \Exception("Команда с таким именем уже зарегистрирована! Задайте другое имя");
        }
//        Иначе зарегистрировать команду
        $command = new Command($command_name, $elements);
        $command->save();

        echo "\nЗарегистрирована новая команда: {$command->getName()}\n";
        echo $command;
        exit;
    }
}