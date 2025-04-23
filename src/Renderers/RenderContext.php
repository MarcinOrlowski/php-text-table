<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable\Renderers;

use Lombok\Getter;
use MarcinOrlowski\TextTable\TextTable;

/**
 * @method int getRenderedRowIdx()
 * @method int getTableRowIdx()
 * @method TextTable getTable()
 */
#[Getter]
class RenderContext extends \Lombok\Helper
{
    public function __construct(protected TextTable $table)
    {
        parent::__construct();
    }

    /** Number of currently rendered **data row** (so actual table data row, no decoration counted) */
    protected int $tableRowIdx = 0;

    /** Increments data row index by one. */
    public function incTableRowIdx(): void
    {
        $this->tableRowIdx++;
    }

    /** Number of currently rendered **visual row** (incl. decoration rows like frames etc) */
    protected int $renderedRowIdx = 0;

    /** Increments rendered row index by one. */
    public function incRenderedRowIdx(): void
    {
        $this->renderedRowIdx++;
    }

    /* ****************************************************************************************** */

    /**
     * @param \Stringable|string|int $columnKey Column key to be checked.
     *
     * @return bool `TRUE` if specified data column is first one to be rendered in a row.
     */
    public function isFirstVisibleColumn(\Stringable|string|int $columnKey): bool
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        foreach ($this->getTable()->getColumns() as $key => $column) {
            if ($column->isVisible()) {
                return $key === $columnKey;
            }
        }
        return false;
    }

    /**
     * @param \Stringable|string|int $columnKey Column key to be checked.
     *
     * @return bool `TRUE` if specified data column is last one to be rendered in a row.
     */
    public function isLastVisibleColumn(\Stringable|string|int $columnKey): bool
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }

        $lastVisibleColumnKey = null;
        foreach ($this->getTable()->getColumns() as $key => $column) {
            if (!$column->isVisible()) {
                continue;
            }
            $lastVisibleColumnKey = $key;
        }
        return $lastVisibleColumnKey === $columnKey;
    }

    /* ****************************************************************************************** */

    /** Returns `TRUE` if currently rendered row is first visible row of the table. */
    public function isFirstVisibleRow(): bool
    {
        return $this->getRenderedRowIdx() === 0;
    }

    /** Returns `TRUE` if currently rendered row is last visible row of the table. */
    public function isLastVisibleRow(): bool
    {
        return $this->getTableRowIdx() === $this->getTable()->getRowCount();
    }
}
