<?php declare(strict_types=1);

namespace Shell\Terminal;

class Cursor
{
    private $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    public function moveUp(int $lines = 1): Cursor
    {
        $this->output->write("\e[{$lines}A");

        return $this;
    }

    public function moveLeft(int $cols = 1): Cursor
    {
        $this->output->write("\e[{$cols}D");

        return $this;
    }

    public function moveToStartOfLine(): Cursor
    {
        $this->output->write("\r");

        return $this;
    }

    public function deleteLine(): Cursor
    {
        $this->output->write("\e[K");

        return $this;
    }

    public function hide(): Cursor
    {
        $this->output->write("\e[?25l");

        return $this;
    }

    public function applyDefaultStyle(): Cursor
    {
        $this->output->write("\e[?25h'");

        return $this;
    }

    public function setStyle(Style $style): Cursor
    {
        $this->output->write((string) $style);

        return $this;
    }

    public function resetStyle(): Cursor
    {
        $this->output->write("\e[0m");

        return $this;
    }
}
