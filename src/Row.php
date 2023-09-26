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

use Lombok\Getter;
use Lombok\Setter;
use MarcinOrlowski\TextTable\Exceptions\DuplicateColumnKeyException;

/**
 * Class represents single row of cells of the table.
 *
 * @method CellsContainer getContainer()
 */
#[Getter]
#[Setter]
class Row extends BaseContainer
{
    /**
     * @param array|null $cells Optional list of cells to be added to the newly created row.
     *
     * @throws DuplicateColumnKeyException
     */
    public function __construct(array|null $cells = null)
    {
        parent::__construct();

        $this->container = new CellsContainer();

        if (!empty($cells)) {
            $this->addCells($cells);
        }
    }

    /* ****************************************************************************************** */

    /** Contains all cells of given row. */
    protected CellsContainer $container;

    /**
     * Adds multiple cells to the row at once. If no explicit cell indices or column keys are
     * specified, cells are added in the same order as they are defined in the array passed as
     * argument.
     *
     * @param array<string|int, Cell|string|int|float|bool|null> $cells Array of cells to be added.
     *
     * @throws DuplicateColumnKeyException
     */
    public function addCells(array $cells): self
    {
        foreach ($cells as $columnKey => $cell) {
            $this->addCell($columnKey, $cell);
        }

        return $this;
    }

    /**
     * Adds single cell to the table row.
     *
     * @param string|int            $columnKey Column key or index we are going to populate.
     * @param Cell|string|float|int $cell      Cell value to be added.
     * @param Align                 $align     Cell content alignment.
     *
     * @return $this Returns instance of self to allow chaining.
     *
     * @throws DuplicateColumnKeyException
     */
    public function addCell(string|int            $columnKey,
                            Cell|string|float|int $cell,
                            Align                 $align = Align::AUTO): self
    {
        if (!($cell instanceof Cell)) {
            $cell = new Cell($cell, $align);
        }
        $this->getContainer()->addCell($columnKey, $cell);

        return $this;
    }

    /* ****************************************************************************************** */
}
