<?php

$library = function ($classname) {
    $path = __DIR__ . "/../{$classname}.php";
    $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
    if (file_exists($path)) {
        require_once ($path);
    }
};

\spl_autoload_register($library);

try {
    \library\Conf::instance();
    //todo: добавить проверку на то, что запуск именно из консоли
//    \library\Parser::readCommand();

    $command = \library\Command::findOne(['name' => "command_name_12"]);
    $command->setName("modified_name");
    $command->setArguments(["modified_arguments"]);
    $command->addOption(new \library\Option("new_option", [
        "new_param_1",
        "new_param_2",
    ]));
    $command->save();
    echo $command;
} catch (\Exception $e) {
    echo $e->getMessage();
}