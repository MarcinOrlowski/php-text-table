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

use MarcinOrlowski\AsciiTable\Output\OutputContract;
use MarcinOrlowski\AsciiTable\Output\Writers\EchoWriter;

class AsciiTable
{
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

    /**
     * @param Row[]|array[] $rows
     */
    public function addRows(array $rows): self
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * Appends row to the end of table.
     *
     * @param Row|array|null $srcRow When `array` is given, then it is expected to be the each of the
     *                               column is the row's cell value. It should be either instance of
     *                               `Cell` class or `string|int`. If primitive is given, the instance
     *                               of `Cell` will automatically be created. The `array` elements should
     *                               be either in form `columnKey => value` or can be all given without
     *                               own keys and then, the proper `columnKey` will be picked based on
     *                               table column definitions (i.e. for `$srcRow` being `['a', 'b']`, the
     *                               `b` value will be put into cell of the 2nd column). This auto-assigment
     *                               works only when `$srcRow` array uses no custom keys **and** table
     *                               column definition are **all** using `string` keys. For all the other
     *                               cases explicit `columnKey` must be given. Passing `null` as `$srcRow`
     *                               causes no effect.
     */
    public function addRow(Row|array|null $srcRow): self
    {
        if ($srcRow === null) {
            return $this;
        }

        $columns = $this->getColumns();

        $row = $srcRow;
        if (!($srcRow instanceof Row)) {
            $row = new Row();

            // If source array has only numeric keys, and column definitions are using non-numeric keys,
            // then it is assumed that source array elements are in sequence and will be automatically
            // assigned to cell at position matching their index in source array.
            $srcHasNumKeysOnly = \count($srcRow) === \count(\array_filter(\array_keys($srcRow), \is_int(...)));
            $columnsHasStringKeysOnly = \count($columns) === \count(\array_filter(\array_keys($columns->toArray()), \is_string(...)));

            if ($srcHasNumKeysOnly && $columnsHasStringKeysOnly) {
                $columnKeys = \array_keys($columns->toArray());

                $srcIdx = 0;
                foreach ($srcRow as $cell) {
                    $columnKey = $columnKeys[ $srcIdx ];
                    $row->addCell($columnKey, $cell);
                    $srcIdx++;
                }
            } else {
                $row->addCells($srcRow);
            }
        }

        $columnOffset = 0;
        foreach ($row as $columnKey => $cell) {
            /**
             * @var string|int $columnKey
             * @var Cell       $cell
             */
            if (!$columns->offsetExists($columnKey)) {
                throw new \InvalidArgumentException(
                    \sprintf('Cannot add cell #%d. Unknown column key: %s', $columnOffset, $columnKey));
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

    public function getColumns(): ColumnsContainer
    {
        return $this->columns;
    }

    public function addColumn(string|int $columnIdx, Column|string $columnVal): self
    {
        if (\is_string($columnVal)) {
            $columnVal = new Column($columnVal);
        } else if (!($columnVal instanceof Column)) {
            throw new \InvalidArgumentException(
                \sprintf('Unsupported column type (%s): %s', \get_debug_type($columnVal), $columnIdx));
        }

        $this->columns->add($columnIdx, $columnVal);

        return $this;
    }

    public function addColumns(array $columns): self
    {
        foreach ($columns as $columnIdx => $columnVal) {
            if (\is_int($columnIdx)) {
                if (\is_string($columnVal)) {
                    $columnIdx = $columnVal;
                } else if ($columnVal instanceof Column) {
                    $columnIdx = $columnVal->getTitle();
                } else {
                    throw new \InvalidArgumentException(
                        'Unsupported column data type: ' . \get_debug_type($columnVal));
                }
            }

            $this->addColumn($columnIdx, $columnVal);
        }

        return $this;
    }

    /* ****************************************************************************************** */

    public function render(?OutputContract $writer = null): void
    {
        if ($writer === null) {
            $writer = new EchoWriter();
        }

        $renderer = new Renderer();
        $renderer->render($this, $writer);
    }

    public function setColumnAlign(string|int $columnKey, Align $align): self
    {
        $this->columns->get($columnKey)->setAlign($align);

        return $this;
    }

    public function setColumnWidth(string|int $columnKey, int $width): self
    {
        $this->columns->get($columnKey)->setMaxWidth($width);

        return $this;
    }

    public function getTotalWidth(): int
    {
        $totalWidth = 0;
        foreach ($this->getColumns() as $column) {
            $totalWidth += $column->getWidth();
        }

        return $totalWidth;
    }

}
