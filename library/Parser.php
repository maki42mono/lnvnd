<?php

namespace library;
use library\exception;

class Parser
{
    private Command $command;
    private bool $not_test_mode = true;

    public function readCommand(string $input = null): void
    {
        if (isset($input)) {
            $inputs = explode(' ', $input);
        } else {
            $inputs = $_SERVER['argv'];
            array_unshift($inputs, null);
        }
        unset($inputs[0]);
        unset($inputs[1]);
        $console_input = implode(' ', $inputs);
        $pattern = '/^(\w+|\d+|\s|\.|\=|\,|\{|\}|\[|\]|\=])*$/i';
        preg_match($pattern, $console_input, $verified_inputs);
//        Если введены недопустимые символы — сгенерировать ошибку
        if ($console_input !== $verified_inputs[0]) {
            throw new \Exception("Вы ввели недопустимые символы. Можно вводить латиницу, цифры, пробелы и симвлы .={}[]",
                500);
        }
        $mode = Mode::instance();
        if ($mode->get("mode") == Mode::TEST_MODE) {
            $this->not_test_mode = false;
        }
        $this->parseInput($console_input);
    }

    private function parseInput(string $input): void
    {
//        Пытаемся получить список всех команд
        if ($input == "") {
            $commands = Command::findAll();
//            Если не зарегистрировано ни одной команды — показать ошибку
            if ($commands == null) {
                throw new exception\NoCommandsException("Не зарегистрировано ни одной команды! Зарегистрируйте что-то, потом выводите",
                    500);
            }

//            Иначе вывести список всех команд
            if ($this->not_test_mode) {
                echo "====Список команд====\n\n";
                foreach ($commands as $command) {
                    echo "{$command}\n";
                    echo "====\n\n";
                }
            }
            return;
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
            $this->command = Command::findOne(['name' => $command_name]);
            if ($this->not_test_mode) {
                echo $this->command;
            }
            return;
        }

//        Если пытаемся зарегистрировать существующею команду — вернуть ошибку
        if (Command::hasOne(['name' => $command_name])) {
            throw new \Exception("Команда с таким именем уже зарегистрирована! Задайте другое имя");
        }
//        Иначе зарегистрировать команду
        $this->command = new Command($command_name, $elements);
        $this->command->save();
        if ($this->not_test_mode) {
            echo "\nЗарегистрирована новая команда: {$this->command->getName()}\n";
            echo $this->command;
        }
    }

    public function getCommand(): Command
    {
        return $this->command;
    }
}