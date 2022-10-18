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

use MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFound;
use MarcinOrlowski\AsciiTable\Exceptions\DuplicateColumnKey;
use MarcinOrlowski\AsciiTable\Exceptions\UnsupportedColumnType;
use MarcinOrlowski\AsciiTable\Output\WriterContract;
use MarcinOrlowski\AsciiTable\Output\Writers\EchoWriter;
use MarcinOrlowski\AsciiTable\Renderers\DefaultRenderer;

class AsciiTable
{
    /**
     * @param array $headerColumns Optional array of column headers to be created.
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
     * @throws UnsupportedColumnType
     */
    public function __construct(array $headerColumns = [])
    {
        $this->init($headerColumns);
    }

    /**
     * @param array $headerColumns Optional array of column headers to be created.
     *
     * @return self
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
     * @throws UnsupportedColumnType
     */
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

    /**
     * Returns table column definitions and meta data container.
     */
    public function getColumns(): ColumnsContainer
    {
        return $this->columns;
    }

    /**
     * Adds new column with specific index. Note columns are registered in the order they are added.
     *
     * @param string|int    $columnKey Unique column key to be assigned to this column
     * @param Column|string $columnVal Either instance of `Column` or string to be used as column title
     *                                 (for which instance of `Column` will be automatically created).
     *
     * @return self
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
     * @throws UnsupportedColumnType
     */
    public function addColumn(string|int $columnKey, Column|string $columnVal): self
    {
        if (\is_string($columnVal)) {
            $columnVal = new Column($columnVal);
        } else if (!($columnVal instanceof Column)) {
            throw new UnsupportedColumnType(
                \sprintf('Unsupported column type (%s): %s', \get_debug_type($columnVal), $columnKey));
        }

        $this->columns->addColumn($columnKey, $columnVal);

        return $this;
    }

    /**
     * Adds multiple columns at once. Note columns are registered in the order they are present in source
     * array.
     *
     * Note that this method **auto-creates** column keys for all **non-string** table keys. For such
     * case the key will be derived either from passed `string` for from `Column` instance' title string.
     * All explicitly specified `string` keys will be preserved and used.
     *
     * @param string[]|Column[] $columns List of columns to be added, given either via instance of `Column`
     *                                   or as string to be used as column title (for which instance of
     *                                   `Column` will be automatically created).
     *
     * @return self
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
     * @throws UnsupportedColumnType
     */
    public function addColumns(array $columns): self
    {
        foreach ($columns as $columnKey => $columnVal) {
            if (\is_int($columnKey)) {
                if (\is_string($columnVal)) {
                    $columnKey = $columnVal;
                } else if ($columnVal instanceof Column) {
                    $columnKey = $columnVal->getTitle();
                } else {
                    throw new \InvalidArgumentException(
                        'Unsupported column data type: ' . \get_debug_type($columnVal));
                }
            }

            $this->addColumn($columnKey, $columnVal);
        }

        return $this;
    }

    /* ****************************************************************************************** */

    /** Holds all table rows */
    protected RowsContainer $rows;

    /**
     * Returns table rows container.
     */
    public function getRows(): RowsContainer
    {
        return $this->rows;
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
     *
     * @return self
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
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

        foreach ($row as $columnKey => $cell) {
            /**
             * @var string|int $columnKey
             * @var Cell       $cell
             */
            // Stretch the column width (if needed and possible) to fit the cell content.
            $columnMeta = $this->columns->getColumn($columnKey);
            $columnMeta->updateMaxWidth(\mb_strlen($cell->getValue()));
        }

        $this->rows[] = $row;

        return $this;
    }

    /**
     * Adds multiple rows in a batch.
     *
     * @param Row[]|array[] $rows
     *
     * @throws ColumnKeyNotFound
     * @throws DuplicateColumnKey
     */
    public function addRows(array $rows): self
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /* ****************************************************************************************** */

    /**
     * Renders given table and outputs it via provided writer.
     *
     * @param WriterContract|null $writer
     *
     * @throws ColumnKeyNotFound
     */
    public function render(?WriterContract $writer = null): void
    {
        if ($writer === null) {
            $writer = new EchoWriter();
        }

        $renderer = new DefaultRenderer();
        $renderer->render($this, $writer);
    }

    public function getColumn(string $columnKey): Column
    {
        return $this->columns->getColumn($columnKey);
    }

    /**
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param Align      $align
     *
     * @return self
     *
     * @throws ColumnKeyNotFound
     */
    public function setDefaultColumnAlign(string|int $columnKey, Align $align): self
    {
        $this->columns->getColumn($columnKey)->setDefaultColumnAlign($align);

        return $this;
    }

    /**
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param int        $width
     *
     * @return self
     */
    public function setColumnMaxWidth(string|int $columnKey, int $width): self
    {
        $this->columns->getColumn($columnKey)->setMaxWidth($width);

        return $this;
    }

    /**
     * @param string|int $columnKey Key of the column to be hidden. Hidding hidden column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFound
     */
    public function hideColumn(string|int $columnKey): self
    {
        $this->columns->getColumn($columnKey)->hide();

        return $this;
    }

    /**
     * @param string|int $columnKey Key of the column to be shown. Showing visible column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFound
     */
    public function showColumn(string|int $columnKey): self
    {
        $this->columns->getColumn($columnKey)->hide();

        return $this;
    }

    /**
     * Get total width of the table WITHOUT edges (so the full table width
     * in output is then 2 (for left edge) + getTotalWidth() + 2 (for right edge).
     */
    public function getTotalWidth(): int
    {
        $totalWidth = 0;

        foreach ($this->getColumns() as $column) {
            $totalWidth += $column->getWidth();
        }

        // Next, we need to account column separators as well.
        $columnSeparator = ' | ';
        $columnCnt = \count($this->getColumns());
        $totalWidth += ($columnCnt - 1) * \mb_strlen($columnSeparator);

        return $totalWidth;
    }

}
