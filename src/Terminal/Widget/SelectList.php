<?php declare(strict_types=1);

namespace Shell\Terminal\Widget;

use Shell\Terminal;
use Shell\Terminal\Style;

class SelectList extends Terminal\Widget
{
    private $options = [];
    private $current = 0;
    private $selected = [];
    private $label = '';
    private $hoverState;
    private $idleState;
    private $draw = false;
    private $multiple = false;
    private $unchecked = '[ ]';
    private $checked = '[▪]';

    public function __construct(Terminal $terminal)
    {
        parent::__construct($terminal);
        $this->hoverState = Style::create();
        $this->idleState = Style::create()->dim();
    }

    public function label(string $label): SelectList
    {
        $this->label = $label;

        return $this;
    }

    public function multiple(bool $multiple = false): SelectList
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function style(
        Style $idleItemStyle,
        Style $hoverItemStyle = null,
        string $unchecked = '[ ]',
        string $checked = '[▪]'
    ): SelectList {
        $this->idleState = $idleItemStyle;
        if ($hoverItemStyle) {
            $this->hoverState = $hoverItemStyle;
        }
        $this->unchecked = $unchecked;
        $this->checked = $checked;

        return $this;
    }

    public function options(string ...$options): SelectList
    {
        $this->options = $options;

        return $this;
    }

    public function value()
    {
        if (!$this->draw) {
            $this->draw();
        }

        while ($char = $this->terminal->readChar()) {
            switch ($char) {
                case Terminal\Keyboard::RETURN:
                    break 2;

                case Terminal\Keyboard::DOWN_ARROW:
                case Terminal\Keyboard::TAB:
                    $this->current++;
                    break;

                case Terminal\Keyboard::SPACE:
                    if (in_array($this->current, $this->selected, true)) {
                        $this->removeItem($this->current);
                    } else {
                        $this->selected[] = $this->current;
                    }
                    break;

                case Terminal\Keyboard::UP_ARROW:
                    $this->current--;
                    break;
                default:
                    continue 2;
            }
            if ($this->current < 0) {
                $this->current = 0;
            }
            if ($this->current >= count($this->options)) {
                $this->current = count($this->options) - 1;
            }
            $this->redraw();
        }

        if ($this->multiple) {
            return $this->selected;
        }

        return $this->current;
    }

    public function draw(): void
    {
        $this->terminal->write($this->label);
        $this->terminal->write(PHP_EOL);
        $this->terminal->write(PHP_EOL);
        foreach ($this->options as $index => $label) {
            $checkbox = $this->unchecked;
            if ((!$this->multiple && $index === $this->current) ||
                ($this->multiple && in_array($index, $this->selected, true))) {
                $checkbox = $this->checked;
            }

            if ($this->current === $index) {
                $label = $this->hoverState . ' ' . $label . "\e[0m";
            } else {
                $label = $this->idleState . ' ' . $label . "\e[0m";
            }

            $this->terminal->write("\t" . $checkbox . $label . "\n");
        }

        $this->draw = true;
    }

    public function height(): int
    {
        return count($this->options) + 2;
    }

    private function removeItem(int $element): void
    {
        if (($key = array_search($element, $this->selected, false)) !== false) {
            array_splice($this->selected, $key, 1);
        }
    }
}
