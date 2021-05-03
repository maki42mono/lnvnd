<?php


namespace library;


abstract class Singleton
{
//    todo: переименовать везде так приватные свойства
    private static array $_instances = [];
    protected mixed $values;

    abstract protected function __construct();

    public static function instance(): self
    {
        $class_name = get_called_class();
        if (!isset(self::$_instances[$class_name])) {
            self::$_instances[$class_name] = new $class_name();
        }

        return self::$_instances[$class_name];
    }

    public function get(string $key): mixed
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
}