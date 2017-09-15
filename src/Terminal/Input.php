<?php declare(strict_types=1);

namespace Shell\Terminal;

use Shell\Exception\InvalidArgumentException;

class Input
{
    private $source;

    public function __construct($source = STDIN)
    {
        if (!is_resource($source)) {
            throw InvalidArgumentException::forInvalidSource($source);
        }

        $this->source = $source;
    }

    public function read(int $length): string
    {
        return fread($this->source, $length);
    }

    public function line(): string
    {
        return trim($this->read(1024));
    }

    public function char(): string
    {
        readline_callback_handler_install('', function() {});
        $char = fread($this->source, 4);
        readline_callback_handler_remove();
        return $char;
    }
}
