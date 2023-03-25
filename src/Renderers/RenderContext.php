<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
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
    protected TextTable $table;

    public function __construct(TextTable $table)
    {
        parent::__construct();

        $this->table = $table;
    }

    /** Number of currently rendered **data row** (so actual table data row, no decoration counted) */
    protected int $tableRowIdx = 0;

    /** Increments data row index by one. */
    public function incTableRowIdx(): self
    {
        $this->tableRowIdx++;
        return $this;
    }

    /** Number of currently rendered **visual row** (incl. decoration rows like frames etc) */
    protected int $renderedRowIdx = 0;

    /** Increments rendered row index by one. */
    public function incRenderedRowIdx(): self
    {
        $this->renderedRowIdx++;
        return $this;
    }

    /* ****************************************************************************************** */

    /** Returns `TRUE` if specified data column is first one to be rendered in a row. */
    public function isFirstVisibleColumn(string|int $columnKey): bool
    {
        foreach ($this->getTable()->getColumns() as $key => $column) {
            if ($column->isVisible()) {
                return $key === $columnKey;
            }
        }
        return false;
    }

    /** Returns `TRUE` if specified data column is last one to be rendered in a row. */
    public function isLastVisibleColumn(string|int $columnKey): bool
    {
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
