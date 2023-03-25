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

class ColumnsContainer extends BaseContainer
{
    /** @var Column[] $container */
    protected array $container = [];

    /**
     * Returns instance of `Column` for given key, or throws exception is no such column exists.
     *
     * @param string|int $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFoundException
     */
    public function getColumn(string|int $columnKey): Column
    {
        if (!$this->offsetExists($columnKey)) {
            throw new ColumnKeyNotFoundException("Unknown column key: {$columnKey}");
        }
        return $this->container[ $columnKey ];
    }

    /**
     * Returns `TRUE` if column referenced by specified key exists, `FALSE` otherwise.
     *
     * @param string|int $columnKey Column key we are going to populate.
     *
     * @return bool
     */
    public function hasColumn(string|int $columnKey): bool
    {
        return $this->offsetExists($columnKey);
    }

    /**
     * Adds new column definition to the container.
     *
     * @param string|int $columnKey Column key we are going to populate.
     * @param Column     $column
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     */
    public function addColumn(string|int $columnKey, Column $column): self
    {
        if ($this->offsetExists($columnKey)) {
            throw new DuplicateColumnKeyException("Column index already exists: {$columnKey}");
        }

        $this->container[ $columnKey ] = $column;

        $this->getColumn($columnKey)->updateMaxWidth(\mb_strlen($column->getTitle()));

        return $this;
    }

    /* ****************************************************************************************** */

}
