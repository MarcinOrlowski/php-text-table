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

use MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFound;
use MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKey;
use MarcinOrlowski\AsciiTable\Exceptions\InvalidColumnType;
use Traversable;

class ColumnsContainer implements \Countable, \ArrayAccess, \IteratorAggregate, ArrayableContract
{
    /** @var Column[] $columns */
    protected array $columns = [];

    /**
     * Returns instance of `Column` for given key, or throws exception is no such column exists.
     *
     * @param string|int $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFound
     */
    public function get(string|int $columnKey): Column
    {
        if (!$this->columnExists($columnKey)) {
            throw new ColumnKeyNotFound("Unknown column key: {$columnKey}");
        }
        return $this->columns[ $columnKey ];
    }

    /**
     * @param string|int $columnKey Column key we are going to populate.
     *
     * @return bool
     */
    public function columnExists(string|int $columnKey): bool
    {
        return \array_key_exists($columnKey, $this->columns);
    }

    /**
     * Adds new column definition to the container.
     *
     * @param string|int $columnKey Column key we are going to populate.
     * @param Column     $column
     *
     * @return self
     *
     * @throws \MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFound
     * @throws \MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKey
     */
    public function add(string|int $columnKey, Column $column): self
    {
        if (\array_key_exists($columnKey, $this->columns)) {
            throw new DuplicateColumnKey("Column index already exists: {$columnKey}");
        }

        $this->columns[ $columnKey ] = $column;

        $this->get($columnKey)->updateMaxWidth(\strlen($column->getTitle()));

        return $this;
    }

    /* ****************************************************************************************** */

    /**
     * Returns total number of columns in the container.
     */
    public function count(): int
    {
        return \count($this->columns);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
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

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Column)) {
            throw new \InvalidArgumentException(
                \sprintf('Expected instance of %s, got %s', Column::class, \get_debug_type($value)));
        }

        if ($offset === null) {
            $this->columns[] = $value;
        } else {
            /** @var string|int $offset */
            $this->columns[ $offset ] = $value;
        }
    }

    /** @inheritDoc */
    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->columns[ $offset ]);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->columns);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
    public function toArray(): array
    {
        return $this->columns;
    }
}
