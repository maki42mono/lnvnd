<?php

$path = __DIR__ . "/../vendor/autoload.php";
$path = str_replace("/", DIRECTORY_SEPARATOR, $path);
require_once ($path);
use PHPUnit\Framework\TestCase;
use \library\Command;
use \library\Parser;
use library\Option;

class ParserTest extends TestCase
{
    public function testParseFullCommand(): void
    {
        $time = time();
        $command_name = "command_name_{$time}";
        $input = "php public\index.php {$command_name} {verbose,overwrite} [log_file=app.log] {unlimited} [methods={create,update,delete}] [paginate=50] {log}";
        $parser = new Parser();
        $parser->readCommand($input, false);
        $command = $parser->getCommand();
        $control_command = new Command($command_name);
        $control_command->setArguments([
            'verbose',
            'overwrite',
            'unlimited',
            'log',
        ]);
        $control_command->setOptions([
            new Option('log_file', ['app.log']),
            new Option('methods', ['create','update','delete']),
            new Option('paginate', ['50']),
        ]);
        $this->assertEquals((string)$command,(string)$control_command);
    }
}