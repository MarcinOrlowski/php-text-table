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

use MarcinOrlowski\TextTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\TextTable\Exceptions\DuplicateColumnKeyException;
use MarcinOrlowski\TextTable\Traits\ArrayAccessTrait;
use MarcinOrlowski\TextTable\Traits\IteratorAggregateTrait;

class CellsContainer implements ContainerContract
{
    use ArrayAccessTrait;
    use IteratorAggregateTrait;

    /** @var Cell[] $container */
    protected array $container = [];

    /**
     * Adds new cell to the row's cell container. Throws exception if cell with given key already exists.
     *
     * @param string|int $columnKey Key of the column we want this cell to belong to.
     * @param Cell       $cell      Instance of `Cell` to be added.
     *
     * @throws DuplicateColumnKeyException
     */
    public function add(string|int $columnKey, Cell $cell): self
    {
        if ($this->offsetExists($columnKey)) {
            throw new DuplicateColumnKeyException("Column key already exists: {$columnKey}");
        }

        $this->container[ $columnKey ] = $cell;
        return $this;
    }

    /**
     * Returns cell for given column key. Throws exception if cell with given key does not exist.
     *
     * @param string|int $columnKey Key of the column we want this cell to belong to.
     *
     * @throws ColumnKeyNotFoundException
     */
    public function get(string|int $columnKey): Cell
    {
        if (!$this->offsetExists($columnKey)) {
            throw new ColumnKeyNotFoundException("Unknown column key: {$columnKey}");
        }
        return $this->container[ $columnKey ];
    }

    /**
     * Returns cell for given column key. Returns null if cell with given key does not exist.
     *
     * @param string|int $columnKey Key of the column we want this cell to belong to.
     *
     * @return bool
     */
    public function has(string|int $columnKey): bool
    {
        return $this->offsetExists($columnKey);
    }

    /* ****************************************************************************************** */

}
