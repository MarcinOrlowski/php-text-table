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
use Traversable;

class CellsContainer implements ContainerContract
{
    /** @var Cell[] $cells */
    protected array $cells = [];

    /**
     * Adds new cell to the row's cell container. Throws exception if cell with given key already exists.
     *
     * @param string|int $columnKey Key of the column we want this cell to belong to.
     * @param Cell       $cell      Instance of `Cell` to be added.
     *
     * @throws DuplicateColumnKey
     */
    public function add(string|int $columnKey, Cell $cell): self
    {
        if (\array_key_exists($columnKey, $this->cells)) {
            throw new DuplicateColumnKey("Column key already exists: {$columnKey}");
        }

        $this->cells[ $columnKey ] = $cell;
        return $this;
    }

    /**
     * Returns cell for given column key. Throws exception if cell with given key does not exist.
     *
     * @param string|int $columnKey Key of the column we want this cell to belong to.
     *
     * @throws ColumnKeyNotFound
     */
    public function get(string|int $columnKey): Cell
    {
        if (!$this->offsetExists($columnKey)) {
            throw new ColumnKeyNotFound("Unknown column key: {$columnKey}");
        }
        return $this->cells[ $columnKey ];
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
    public function count(): int
    {
        return \count($this->cells);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
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

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Cell)) {
            throw new \InvalidArgumentException(
                \sprintf('Expected instance of %s, got %s', Cell::class, \get_debug_type($value)));
        }

        if ($offset === null) {
            $this->cells[] = $value;
        } else {
            /** @var string|int $offset */
            $this->cells[ $offset ] = $value;
        }
    }

    /** @inheritDoc */
    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->cells[ $offset ]);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->cells);
    }

    /* ****************************************************************************************** */

    /** @inheritDoc */
    public function toArray(): array
    {
        return $this->cells;
    }
}
