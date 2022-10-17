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

class ColumnsContainer implements \Countable, \ArrayAccess, \IteratorAggregate, ArrayableContract
{
    /** @var Column[] $columns */
    protected array $columns = [];

    public function get(string|int $columnIdx): Column
    {
        if (!$this->columnExists($columnIdx)) {
            throw new \OutOfBoundsException("Unknown column index: {$columnIdx}");
        }
        return $this->columns[ $columnIdx ];
    }

    public function columnExists(string|int $columnIdx): bool
    {
        return \array_key_exists($columnIdx, $this->columns);
    }

    public function add(string|int $columnIdx, Column $column): self
    {
        if (\array_key_exists($columnIdx, $this->columns)) {
            throw new \InvalidArgumentException("Column index already exists: {$columnIdx}");
        }

        $this->columns[ $columnIdx ] = $column;
        return $this;
    }

    /* ****************************************************************************************** */

    public function count(): int
    {
        return \count($this->columns);
    }

    /* ****************************************************************************************** */

    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return \array_key_exists($offset, $this->columns);
    }

    /**
     * @return Column
     */
    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->columns[ $offset ];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Column)) {
            throw new \InvalidArgumentException('Invalid column type: ' . \get_debug_type($value));
        }

        if ($offset === null) {
            $this->columns[] = $value;
        } else {
            /** @var string|int $offset */
            $this->columns[ $offset ] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->columns[ $offset ]);
    }

    /* ****************************************************************************************** */

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->columns);
    }

    /* ****************************************************************************************** */

    public function toArray(): array
    {
        return $this->columns;
    }
}
