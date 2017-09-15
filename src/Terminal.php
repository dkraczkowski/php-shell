<?php declare(strict_types=1);

namespace Shell;

use Shell\Terminal\Cursor;
use Shell\Terminal\Input;
use Shell\Terminal\Output;
use Shell\Exception\RuntimeException;

class Terminal
{
    private $input;
    private $output;
    private $error;
    private $cursor;
    private $readArguments = false;
    private $arguments = [];
    private $path = '';

    public function __construct(Input $input, Output $output, Output $error = null)
    {
        $this->input = $input;
        $this->output = $output;
        $this->error = $error;
        $this->cursor = new Cursor($output);
    }

    public function write(string $output): void
    {
        $this->output->write($output);
    }

    public function error(string $output): void
    {
        $this->error->write($output);
    }

    public function readLine(): string
    {
        return $this->input->line();
    }

    public function readChar(): string
    {
        return $this->input->char();
    }

    public function cursor(): Cursor
    {
        return $this->cursor;
    }

    public function disableInput(): void
    {
        readline_callback_handler_install('', function() {});
    }

    public function enableInput(): void
    {
        readline_callback_handler_remove();
    }

    public function getArguments(): array
    {
        if (!$this->readArguments) {
            $this->readCliArguments();
        }

        return $this->arguments;
    }

    public function getPath(): string
    {
        if (!$this->readArguments) {
            $this->readCliArguments();
        }

        return $this->path;
    }

    private function readCliArguments(): void
    {
        $rawArguments = $this->retrieveCliArguments();
        array_shift($rawArguments);
        $path = true;
        foreach ($rawArguments as $argument) {
            if (0 === strpos($argument, '--')) {
                $eq = strpos($argument, '=');
                if ($eq !== false) {
                    $this->arguments[substr($argument, 2, $eq - 2)] = substr($argument, $eq + 1);
                } else {
                    $k = substr($argument, 2);
                    if (!isset($this->arguments[$k])) {
                        $this->arguments[$k] = true;
                    }
                }
                $path = false;
            } else if (0 === strpos($argument, '-')) {
                if ($argument[2] === '=') {
                    $this->arguments[$argument[1]] = substr($argument, 3);
                } else {
                    foreach (str_split(substr($argument, 1)) as $k) {
                        if (!isset($this->arguments[$k])) {
                            $this->arguments[$k] = true;
                        }
                    }
                }
                $path = false;
            } elseif ($path) {
                $this->path .= ' ' . $argument;
            }
        }

        $this->readArguments = true;
    }

    private function retrieveCliArguments(): array
    {
        global $argv;
        static $commandLineArguments;

        if ($commandLineArguments) {
            return $commandLineArguments;
        }

        if (is_array($argv)) {
            return $argv;
        }

        if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            return $_SERVER['argv'];
        }

        if (isset($GLOBALS['HTTP_SERVER_VARS']['argv']) && is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
            return $GLOBALS['HTTP_SERVER_VARS']['argv'];
        }

        throw RuntimeException::forNoCommandLineArguments();
    }
}
