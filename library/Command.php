<?php

namespace library;

class Command
{
    public static function test(): void
    {
        echo "Hello world!";
    }

    public static function readCommand(): void
    {
        $command = (string)readline();
        echo "Your command: {$command}";
    }
}