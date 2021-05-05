<?php


namespace library;


class Mode extends Singleton
{
    public const TEST_MODE = "TEST_MODE";
    public const PRODUCTION = "PRODUCTION";
    public const EMPTY_TABLES = "EMPTY_TABLES";

    protected function __construct(string $mode = null)
    {
    }
}