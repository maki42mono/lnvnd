<?php
$library = function ($classname) {
    $path = __DIR__ . "/../{$classname}.php";
    $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
    if (file_exists($path)) {
        require_once ($path);
    }
};

\spl_autoload_register($library);

//todo: добавить проверку на то, что запуск именно из консоли
$parser = new \library\Parser();

try {
    \library\Conf::instance();
    $parser->readCommand();
    $command = $parser->getCommand();
    echo $command;
} catch (\Exception $e) {
    echo $e->getMessage();
}