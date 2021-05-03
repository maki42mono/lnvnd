<?php


namespace library;


class CommandMapper extends Mapper
{
    protected function doCreateObject(array $raw)
    {
        return new NewsModel($raw);;
    }

    protected function targetTable(): string
    {
        return "command";
    }
}