<?php declare(strict_types=1);

namespace Shell\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function forInvalidStyleFormat(string $format): InvalidArgumentException
    {
        return new self("Unknown format ${format} passed to style.");
    }

    public static function forInvalidSource($passed): InvalidArgumentException
    {
        $passed = var_export($passed, true);
        return new self("Invalid stream ${passed} provided, expected valid file handler.");
    }

    public static function forInvalidColor(string $passed, array $validColorList): InvalidArgumentException
    {
        return new self("Invalid color ${passed} passed to style, expected one of: " . implode(',', $validColorList));
    }
}
