<?php


namespace library;


class CommandMapper extends Mapper
{
    protected function doCreateObject(array $raw): DomainObject
    {
        $options = explode(';', $raw["options"]);
        $command = new Command($raw["name"], array_merge([$raw["arguments"]], $options));
        $command->setId($raw["id"]);
        return $command;
    }

    protected function targetTable(): string
    {
        return "command";
    }
}