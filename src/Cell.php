<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

class Cell
{
    public function __construct(\Stringable|string|int|float|bool|null $value = '',
                                Align                                  $align = Align::AUTO)
    {
        $this->setValue($value);
        $this->setAlign($align);
    }

    /* ****************************************************************************************** */

    /** String representation of cell's value. */
    protected string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    protected function setValue(\Stringable|string|int|float|bool|null $value): self
    {
        if ($value === null) {
            $value = 'NULL';
        } elseif ($value instanceof \Stringable) {
            $value = $value->__toString();
        } elseif (\is_int($value)) {
            $value = (string)$value;
        } elseif (\is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        } elseif (\is_float($value)) {
            $value = (string)$value;
        }

        $this->value = $value;
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
