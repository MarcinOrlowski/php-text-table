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

use MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKeyException;
use MarcinOrlowski\AsciiTable\Traits\ArrayAccessTrait;
use MarcinOrlowski\AsciiTable\Traits\IteratorAggregateTrait;

class Row implements ContainerContract
{
    use ArrayAccessTrait;
    use IteratorAggregateTrait;

    /**
     * @param array|null $cells Optional list of cells to be added to the newly created row.
     *
     * @throws DuplicateColumnKeyException
     */
    public function __construct(array|null $cells = null)
    {
        $this->container = new CellsContainer();

        if (!empty($cells)) {
            foreach ($cells as $columnKey => $cell) {
                $this->addCell($columnKey, $cell);
            }
        }
    }

    /* ****************************************************************************************** */

    protected CellsContainer $container;

    public function getContainer(): CellsContainer
    {
        return $this->container;
    }

    /**
     * @param array<string|int, Cell|string|int|float|bool|null> $cells
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
     * @param string|int      $columnKey
     * @param Cell|string|int $cell
     * @param Align           $align
     *
     * @return $this
     * @throws \MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKeyException
     */
    public function addCell(string|int      $columnKey,
                            Cell|string|int $cell,
                            Align           $align = Align::AUTO): self
    {
        if (!($cell instanceof Cell)) {
            $cell = new Cell($cell, $align);
        }
        $this->getContainer()->add($columnKey, $cell);

        return $this;
    }

    /* ****************************************************************************************** */
}
