<?php declare(strict_types=1);

namespace Shell\Terminal;

use Shell\Exception\InvalidArgumentException;

class Output
{
    private $source;

    public function __construct($source = STDOUT)
    {
        if (!is_resource($source)) {
            throw InvalidArgumentException::forInvalidSource($source);
        }

        $this->source = $source;
    }

    public function write(string $output): void
    {
        fwrite($this->source, $output);
    }
}

