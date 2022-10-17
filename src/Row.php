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

use Traversable;

class Row implements \ArrayAccess, \IteratorAggregate
{
    public function __construct(array|null $cells = null)
    {
        $this->cells = new CellsContainer();

        if (!empty($cells)) {
            foreach ($cells as $columnKey => $cell) {
                $this->addCell($columnKey, $cell);
            }
        }
    }

    /* ****************************************************************************************** */

    protected CellsContainer $cells;

    public function getCells(): CellsContainer
    {
        return $this->cells;
    }

    /**
     * @param array<string|int, Cell|string|int> $cells
     */
    public function addCells(array $cells): self
    {
        foreach ($cells as $columnKey => $cell) {
            $this->addCell($columnKey, $cell);
        }

        return $this;
    }

    public function addCell(string|int      $columnKey,
                            Cell|string|int $cell,
                            Align           $align = Align::AUTO,
                            Span|int        $columnSpan = Span::NONE): self
    {
        if (!($cell instanceof Cell)) {
            $cell = new Cell($cell, $align, $columnSpan);
        }
        $this->cells->add($columnKey, $cell);

        return $this;
    }

    /* ****************************************************************************************** */

    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return $this->cells->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->cells->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        /** @var string|int $offset */
        $this->cells->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        $this->cells->offsetUnset($offset);
    }

    /* ****************************************************************************************** */

    public function getIterator(): Traversable
    {
        return $this->cells->getIterator();
    }
}
