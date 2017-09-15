<?php declare(strict_types=1);

namespace Shell\Terminal\Widget;

use Shell\Terminal;
use Shell\Terminal\Style;

class ProgressBar extends Terminal\Widget
{
    private $total;
    private $progress;
    private $length = 20;
    private $label = ':value';
    private $filled = '█';
    private $blank = '░';
    private $before = '│';
    private $after = '│';
    private $barStyle;
    private $labelStyle;
    private $draw = false;

    public function __construct(int $total, Terminal $terminal)
    {
        parent::__construct($terminal);
        $this->total = $total;
        $this->barStyle = Style::create();
        $this->labelStyle = Style::create();
    }

    public function style(
        Style $barStyle = null,
        Style $labelStyle = null,
        string $filled = '█',
        string $blank = '░',
        string $before = '│',
        string $after = '│'
    ): ProgressBar {
        if (null !== $barStyle) {
            $this->barStyle = $barStyle;
        }
        if (null !== $labelStyle) {
            $this->labelStyle = $labelStyle;
        }

        $this->filled = $filled;
        $this->blank = $blank;
        $this->before = $before;
        $this->after = $after;

        return $this;
    }

    public function length(int $length = 20): ProgressBar
    {
        $this->length = $length;

        return $this;
    }

    public function label(string $label = ':value', Style $style = null): ProgressBar
    {
        $this->label = $label;
        if ($style) {
            $this->labelStyle = $style;
        }
    }

    public function value(): int
    {
        if (!$this->progress) {
            return 0;
        }
        return (int) ceil($this->progress / $this->total * 100);
    }

    public function progress(int $progress): ProgressBar
    {
        $this->progress = $progress;
        if ($this->draw) {
            $this->redraw();
        } else {
            $this->draw();
        }

        return $this;
    }

    public function draw(): void
    {
        $this->terminal->write("\n");
        $progress = (string) $this->value();
        $barLength = 0;
        if ($progress) {
            $barLength = floor($this->progress / $this->total * $this->length);
        }

        // Label.
        if (strlen($progress) < 2) {
            $progress = ' ' . $progress;
        }
        $label = str_replace(':value', $progress . '%', $this->label);
        $this->terminal->write($this->labelStyle . $label  . "\t");
        $this->terminal->write($this->before);
        $this->terminal->setStyle($this->barStyle);
        for ($i = 0; $i < $this->length; $i++) {
            $char = $this->blank;
            if ($i < $barLength) {
                $char = $this->filled;
            }
            if ($this->barStyle) {
                $this->terminal->write($char);
            } else {
                $this->terminal->write($char);
            }
        }
        $this->terminal->resetStyle();
        $this->terminal->write($this->after);
        $this->terminal->write("\r\n");
        $this->draw = true;
    }

    public function height(): int
    {
        return 2;
    }
}
