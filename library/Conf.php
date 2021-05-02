<?php


namespace library;


class Conf extends Singleton
{

    protected function __construct()
    {
        $this->getConf();
    }

    public static function instance(): self
    {
        return parent::_instance(self::class);
    }

    private function getConf()
    {
        $conf_path = __DIR__ . "/../config/main-local.php";
        if (file_exists($conf_path)) {
            $conf = include_once ($conf_path);
            $this->values = $conf;
        } else {
            throw new \Exception("Создайте файл /config/main-local.php");
        }
    }

    protected function targetClass(): string
    {
        return self::class;
    }
}