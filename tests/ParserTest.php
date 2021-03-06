<?php declare(strict_types=1);
$path = __DIR__ . "/../vendor/autoload.php";
$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
require_once($path);

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private library\Command $command;
    private library\Mode $mode;

    public function setUp(): void
    {
        $this->mode = library\Mode::instance();
        $this->mode->set("mode", library\Mode::TEST_MODE);
        parent::setUp();
    }

    public function tearDown(): void
    {
        if (isset($this->command)) {
            $this->command->delete();
        }
        parent::tearDown();
    }

    public function testHelpSuccess(): void
    {
        $command_name = "command_" . time();
        $input_new_command = "php public\index.php {$command_name} {verbose,overwrite}";
        $parser = new library\Parser();
        $parser->readCommand($input_new_command, false);
        $this->command = $parser->getCommand();
        $input_help = "php public\index.php {$command_name} {help}";
        $parser->readCommand($input_help, false);
        $helped_command = $parser->getCommand();
        $this->assertEquals((string)$this->command,(string)$helped_command);
    }

    public function testNoCommandsRegistered(): void
    {
        $this->mode->set("tables", library\Mode::EMPTY_TABLES);
        $input = "php public\index.php";
        $parser = new library\Parser();
        try {
            $parser->readCommand($input, false);
            $this->fail("Ожидалась ошибка типа NoCommandsException");
        } catch (\Exception $e) {
            $this->assertInstanceOf(library\exception\NoCommandsException::class, $e);
        }
    }

    public function testParseFullCommand(): void
    {
        $time = time();
        $command_name = "command_name_{$time}";
        $input = "php public\index.php {$command_name} {verbose,overwrite} [log_file=app.log] {unlimited} [methods={create,update,delete}] [paginate=50] {log}";
        $parser = new library\Parser();
        $parser->readCommand($input, false);
        $this->command = $parser->getCommand();
        $control_command = new library\Command($command_name);
        $control_command->setArguments([
            'verbose',
            'overwrite',
            'unlimited',
            'log',
        ]);
        $control_command->setOptions([
            new library\Option('log_file', ['app.log']),
            new library\Option('methods', ['create','update','delete']),
            new library\Option('paginate', ['50']),
        ]);
        $this->assertEquals((string)$this->command,(string)$control_command);
    }
}