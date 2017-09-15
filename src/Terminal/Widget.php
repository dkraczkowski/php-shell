<?php declare(strict_types=1);

namespace Shell\Terminal;

use Shell\Terminal;

abstract class Widget
{
    protected $terminal;

    abstract public function draw(): void;
    abstract public function height(): int;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function redraw(): void
    {
        $this->remove();
        $this->draw();
    }

    public function remove(): void
    {
        for ($i = 0; $i < $this->height(); $i++) {
            $this->terminal->cursor()->moveUp()->deleteLine();
        }
    }
}
