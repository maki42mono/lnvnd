<?php
$library = function ($classname) {
    $path = __DIR__ . "/../{$classname}.php";
    $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
    if (file_exists($path)) {
        require_once ($path);
    }
};

\spl_autoload_register($library);

\library\Command::test();
\library\Command::readCommand();