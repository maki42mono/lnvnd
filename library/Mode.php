<?php


namespace library;


class Mode extends Singleton
{
    public const TEST_MODE = "TEST_MODE";
    public const PRODUCTION = "PRODUCTION";

    protected function __construct(string $mode = null)
    {
    }
}