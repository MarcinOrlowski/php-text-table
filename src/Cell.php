<?php
declare(strict_types=1);

/**
 * ASCII Table
 *
 * @package   MarcinOrlowski\AsciiTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-ascii-table
 */

namespace MarcinOrlowski\AsciiTable;

class Cell
{
    public function __construct(\Stringable|string|int $value,
                                Align                  $align = Align::AUTO,
                                Span|int               $columnSpan = Span::NONE)
    {
        $this->setValue($value);
        $this->setColumnSpan($columnSpan);
        $this->setAlign($align);
    }

    /* ****************************************************************************************** */

    protected string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    protected function setValue(\Stringable|string|int $value): self
    {
        if ($value instanceof \Stringable) {
            $value = $value->__toString();
        } elseif (\is_int($value)) {
            $value = (string)$value;
        }
        $this->value = $value;
        return $this;
    }

    /* ****************************************************************************************** */

    protected Span|int $columnSpan = Span::NONE;

    public function getColumnSpan(): Span|int
    {
        return $this->columnSpan;
    }

    protected function setColumnSpan(Span|int $columnSpan): self
    {
        $this->columnSpan = $columnSpan;
        return $this;
    }

    /* ****************************************************************************************** */

    protected Align $align = Align::AUTO;

    public function getAlign(): Align
    {
        return $this->align;
    }

    protected function setAlign(Align $align): self
    {
        $this->align = $align;
        return $this;
    }

}
