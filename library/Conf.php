<?php


namespace library;


class Conf extends Singleton
{

    protected function __construct()
    {
        $conf_path = __DIR__ . "/../config/main-local.php";
        if (file_exists($conf_path)) {
            $conf = include_once ($conf_path);
            $this->values = $conf;
        } else {
            throw new \Exception("Создайте файл /config/main-local.php");
        }
    }
}