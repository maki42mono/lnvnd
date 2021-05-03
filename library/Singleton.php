<?php


namespace library;


abstract class Singleton
{
    private static array $instances = [];
    protected mixed $values;

    abstract protected function __construct();

    public static function instance(): self
    {
        $class_name = get_called_class();
        if (!isset(self::$instances[$class_name])) {
            self::$instances[$class_name] = new $class_name();
        }

        return self::$instances[$class_name];
    }

    public function get(string $key): mixed
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
}