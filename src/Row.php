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

use MarcinOrlowski\AsciiTable\Traits\ArrayAccessTrait;
use Traversable;

class Row implements ContainerContract
{
    use ArrayAccessTrait;

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
     */
    public function addCells(array $cells): self
    {
        foreach ($cells as $columnKey => $cell) {
            $this->addCell($columnKey, $cell);
        }

        return $this;
    }

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
