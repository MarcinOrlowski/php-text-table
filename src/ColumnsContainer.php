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

use MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKeyException;
use MarcinOrlowski\AsciiTable\Traits\ArrayAccessTrait;
use MarcinOrlowski\AsciiTable\Traits\IteratorAggregateTrait;

class ColumnsContainer implements ContainerContract
{
    use ArrayAccessTrait;
    use IteratorAggregateTrait;

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
     * Adds new column definition to the container.
     *
     * @param string|int $columnKey Column key we are going to populate.
     * @param Column     $column
     *
     * @return self
     *
     * @throws \MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFoundException
     * @throws \MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKeyException
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
