<?php


namespace library;


class CommandMapper extends Mapper
{
    protected function doCreateObject(array $raw): DomainObject
    {
        $name = $raw["name"];
        unset($raw["name"]);
        return new Command($name);
    }

    protected function targetTable(): string
    {
        return "command";
    }
}