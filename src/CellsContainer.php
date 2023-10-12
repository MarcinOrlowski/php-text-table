<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

use MarcinOrlowski\TextTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\TextTable\Exceptions\DuplicateColumnKeyException;

/**
 * This class is used to store cells for given row.
 */
class CellsContainer extends BaseContainer
{
    /** @var Cell[] $container */
    protected mixed $container = [];

    /**
     * Adds new cell to the row's cell container. Throws exception if cell with given key already
     * exists.
     *
     * @param \Stringable|string|float|int $columnKey Key of the column we want it to belong to.
     * @param Cell                         $cell      Instance of `Cell` to be added.
     *
     * @return CellsContainer
     * @throws DuplicateColumnKeyException
     */
    public function addCell(\Stringable|string|float|int $columnKey, Cell $cell): self
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }

        if ($this->offsetExists($columnKey)) {
            throw DuplicateColumnKeyException::forColumnKey($columnKey);
        }

        $this->container[$columnKey] = $cell;

        return $this;
    }

    /**
     * Returns cell for given column key. Throws exception if cell with given key does not exist.
     *
     * @param \Stringable|string|int $columnKey Key of the column we want this cell to belong to.
     *
     * @return Cell
     * @throws ColumnKeyNotFoundException
     */
    public function getCell(\Stringable|string|int $columnKey): Cell
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        if (!$this->offsetExists($columnKey)) {
            throw ColumnKeyNotFoundException::forColumnKey($columnKey);
        }

        return $this->container[$columnKey];
    }

    /**
     * Returns cell for given column key. Returns null if cell with given key does not exist.
     *
     * @param \Stringable|string|int $columnKey Key of the column we want this cell to belong to.
     *
     * @return bool
     */
    public function hasCell(\Stringable|string|int $columnKey): bool
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        return $this->offsetExists($columnKey);
    }

    /* ****************************************************************************************** */

}
