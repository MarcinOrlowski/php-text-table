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

class Cells implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var Cell[] $cells */
    protected array $cells = [];

    public function add(string|int $columnKey, Cell $cell): self
    {
        if (\array_key_exists($columnKey, $this->cells)) {
            throw new \InvalidArgumentException("Column key already exists: {$columnKey}");
        }

        $this->cells[ $columnKey ] = $cell;
        return $this;
    }

    /* ****************************************************************************************** */

    public function count(): int
    {
        return \count($this->cells);
    }

    /* ****************************************************************************************** */

    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return \array_key_exists($offset, $this->cells);
    }

    /**
     * @return Cell
     */
    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->cells[ $offset ];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Cell)) {
            throw new \InvalidArgumentException('Invalid cell type: ' . \get_debug_type($value));
        }

        /** @var string|int $offset */
        $this->cells[ $offset ] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->cells[ $offset ]);
    }

    /* ****************************************************************************************** */

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->cells);
    }
}
