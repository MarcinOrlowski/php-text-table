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

class Table
{
    /* ****************************************************************************************** */

    public function __construct(array $headerColumns = [])
    {
        $this->init($headerColumns);
    }

    public function init(array $headerColumns = []): self
    {
        $this->columns = new ColumnsContainer();
        $this->rows = new RowsContainer();
        $this->addColumns($headerColumns);

        return $this;
    }

    /* ****************************************************************************************** */

    /** Table column definitions and meta data */
    protected ColumnsContainer $columns;

    /* ****************************************************************************************** */

    protected RowsContainer $rows;

    public function getRows(): RowsContainer
    {
        return $this->rows;
    }

    public function addRow(Row|array|null $srcRow): self
    {
        if ($srcRow === null) {
            return $this;
        }

        $row = \is_array($srcRow)
            ? new Row($srcRow)
            : $srcRow;

        $columnOffset = 0;
        foreach ($row as $columnKey => $cell) {
            /**
             * @var string|int $columnKey
             * @var Cell       $cell
             */
            if (!$this->getColumns()->offsetExists($columnKey)) {
                throw new \InvalidArgumentException(
                    \sprintf('Cannot add cell #%d. Unknown column index: %s', $columnOffset, $columnKey));
            }

            $this->calculateMaxColumnWidth($columnKey, $cell);
            $columnOffset++;
        }

        $this->rows[] = $row;

        return $this;
    }


    protected function calculateMaxColumnWidth(string|int $columnKey, Cell $cell): void
    {
        $columnMeta = $this->columns->get($columnKey);
        $columnMeta->updateMaxWidth(\strlen($cell->getValue()));
    }

    public function getRowCount(): int
    {
        return \count($this->getRows());
    }

    /* ****************************************************************************************** */

    public function addColumns(array $columns): self
    {
        foreach ($columns as $columnIdx => $columnVal) {
            /** @var string|int $targetColumnIdx */
            $targetColumnIdx = $columnVal;

            if (\is_string($columnIdx)) {
                $targetColumnIdx = $columnIdx;
            }

            if (\is_string($columnVal)) {
                $columnVal = new Column($columnVal);
            } else if (!($columnVal instanceof Column)) {
                throw new \InvalidArgumentException(
                    "Unsupported column type: " . \get_debug_type($columnVal));
            }

            $this->addColumn($targetColumnIdx, $columnVal);
        }

        return $this;
    }

    public function addColumn(string|int $columnIdx, Column|string $column): self
    {
        if (\is_string($column)) {
            $column = new Column($column);
        } else if (!($column instanceof Column)) {
            throw new \InvalidArgumentException(
                \sprintf('Unsupported column type (%s): %s', \get_debug_type($column), $columnIdx));
        }

        $this->columns->add($columnIdx, $column);

        return $this;
    }

    public function getColumns(): ColumnsContainer
    {
        return $this->columns;
    }

    public function render(OutputContract $writer): void
    {
        $renderer = new Renderer();
        $renderer->render($this, $writer);
    }

}
