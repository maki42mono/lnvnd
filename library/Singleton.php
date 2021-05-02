<?php


namespace library;


abstract class Singleton
{
    protected static self|null $instance = null;
    protected array $values;

    abstract protected function __construct();
    abstract public static function instance();

    public static function _instance(string $class_name = null): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new $class_name();
        }

        return self::$instance;
    }

    public function get(string $key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
}