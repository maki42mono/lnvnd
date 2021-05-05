<?php declare(strict_types=1);

$path = __DIR__ . "/../vendor/autoload.php";
require_once($path);

try {
    library\Conf::instance();
//    Если запрос из браузера — то найти существующую команду, поменять у нее имя, аргументы и добавить опции
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $command_name = "command_name_12";
        if (library\Command::hasOne(['name' => $command_name])) {
            $command = library\Command::findOne(['name' => $command_name]);
            $time = time();
            $command->setName("modified_name{$time}");
            $command->setArguments(["modified_arguments"]);
            $command->addOption(new library\Option("new_option", [
                "new_param_1",
                "new_param_2",
            ]));
            $command->save();
            echo $command;
        } else {
            echo "Команда {$command_name} не зарегистрирована. Попробуйте зарегистрировать через консоль =)";
        }

    } else {
//        Иначе получить команду из консоли
        $parser = new library\Parser();
        $parser->readCommand();
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}