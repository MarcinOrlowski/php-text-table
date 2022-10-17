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

class RowsContainer implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableContract
{
    /** @var Row[] $rows */
    protected array $rows = [];

    /* ****************************************************************************************** */

    public function count(): int
    {
        return \count($this->rows);
    }

    /* ****************************************************************************************** */

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->rows);
    }

    /* ****************************************************************************************** */

    public function offsetExists(mixed $offset): bool
    {
        /** @var string|int $offset */
        return \array_key_exists($offset, $this->rows);
    }

    public function offsetGet(mixed $offset): mixed
    {
        /** @var string|int $offset */
        return $this->rows[ $offset ];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Row)) {
            throw new \InvalidArgumentException('Invalid row type: ' . \get_debug_type($value));
        }

        if ($offset === null) {
            $this->rows[] = $value;
        } else {
            /** @var string|int $offset */
            $this->rows[ $offset ] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        /** @var string|int $offset */
        unset($this->rows[ $offset ]);
    }

    /* ****************************************************************************************** */

    public function toArray(): array
    {
        return $this->rows;
    }
}
