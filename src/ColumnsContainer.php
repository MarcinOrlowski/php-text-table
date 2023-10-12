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
use MarcinOrlowski\TextTable\Utils\StringUtils;

/**
 * Class contains definitions of columns used by the table.
 */
class ColumnsContainer extends BaseContainer
{
    /** @var Column[] $container */
    protected mixed $container = [];

    /**
     * Returns instance of `Column` for given key, or throws exception is no such column exists.
     *
     * @param \Stringable|string|int $columnKey Column key we are going to populate.
     *
     * @return Column
     * @throws ColumnKeyNotFoundException
     */
    public function getColumn(\Stringable|string|int $columnKey): Column
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        $columnKey = StringUtils::sanitizeColumnKey($columnKey);
        if (!$this->hasColumn($columnKey)) {
            throw ColumnKeyNotFoundException::forColumnKey($columnKey);
        }

        return $this->container[$columnKey];
    }

    /**
     * Returns `TRUE` if column referenced by specified key exists, `FALSE` otherwise.
     *
     * @param \Stringable|string|int $columnKey Column key we are going to populate.
     *
     * @return bool
     */
    public function hasColumn(\Stringable|string|int $columnKey): bool
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        $columnKey = StringUtils::sanitizeColumnKey($columnKey);

        return $this->offsetExists($columnKey);
    }

    /**
     * Adds new column definition to the container.
     *
     * @param \Stringable|string|int $columnKey Column key we are going to populate.
     * @param Column                 $column
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     */
    public function addColumn(\Stringable|string|int $columnKey, Column $column): self
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }

        $columnKey = StringUtils::sanitizeColumnKey($columnKey);

        if ($this->hasColumn($columnKey)) {
            throw DuplicateColumnKeyException::forColumnKey($columnKey);
        }

        $this->container[$columnKey] = $column;

        $this->getColumn($columnKey)->updateMaxWidth(\mb_strlen($column->getTitle()));

        return $this;
    }

}
