<?php declare(strict_types=1);

namespace Shell\Exception;

class RuntimeException extends \RuntimeException
{
    public static function forNoCommandLineArguments(): RuntimeException
    {
        return new self('Could not read command line arguments (register_argc_argv=Off?)');
    }
}
