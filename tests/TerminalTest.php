<?php declare(strict_types=1);

namespace ShellTests;

use PHPUnit\Framework\TestCase;
use Shell\Terminal;

final class TerminalTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $terminal = new Terminal(new Terminal\Input(), new Terminal\Output());

        self::assertInstanceOf(Terminal::class, $terminal);
    }
}
