<?php


namespace library;


class CommandMapper extends Mapper
{
    protected function doCreateObject(array $raw): DomainObject
    {
        $options = explode(';', $raw["options"]);
        return new Command($raw["name"], array_merge([$raw["arguments"]], $options));
    }

    protected function targetTable(): string
    {
        return "command";
    }
}