<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @package   MarcinOrlowski\TextTable
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2022 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

use Lombok\Getter;
use MarcinOrlowski\TextTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\TextTable\Exceptions\DuplicateColumnKeyException;
use MarcinOrlowski\TextTable\Exceptions\NoVisibleColumnsException;
use MarcinOrlowski\TextTable\Exceptions\UnsupportedColumnTypeException;
use MarcinOrlowski\TextTable\Renderers\FancyRenderer;
use MarcinOrlowski\TextTable\Renderers\RendererContract;

/**
 * @method ColumnsContainer getColumns()
 * @method RowsContainer getRows()
 */
#[Getter]
class TextTable extends \Lombok\Helper
{
    /**
     * @param array $headerColumns Optional array of column headers to be created.
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
     */
    public function __construct(array $headerColumns = [])
    {
        parent::__construct();
        $this->init($headerColumns);
    }

    /**
     * @param array $headerColumns Optional array of column headers to be created.
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
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
    #[Getter]
    protected ColumnsContainer $columns;

    /**
     * Adds new column with specific index. Note columns are registered in the order they are added.
     *
     * @param string|int    $columnKey Unique column key to be assigned to this column.
     * @param Column|string $columnVal Either instance of `Column` or string to be used as column title
     *                                 (for which instance of `Column` will be automatically created).
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
     */
    public function addColumn(string|int $columnKey, Column|string $columnVal): self
    {
        if (\is_string($columnVal)) {
            $columnVal = new Column($columnVal);
        } else if (!($columnVal instanceof Column)) {
            throw new UnsupportedColumnTypeException(
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
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
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

    /** Holds all table rows. */
    #[Getter]
    protected RowsContainer $rows;

    /**
     * Returns number of data rows (not including table header)
     */
    public function getRowCount(): int
    {
        return \count($this->rows);
    }

    /**
     * Appends new row to the end of table.
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
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
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
            $columnsHasStringKeysOnly = \count($columns) === \count(
                    \array_filter(\array_keys($columns->toArray()), \is_string(...)));

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

        /** @var Cell[] $row */
        foreach ($row as $columnKey => $cell) {
            /**  @var string|int $columnKey */
            // Stretch the column width (if needed and possible) to fit the cell content.
            $this->getColumn($columnKey)->updateMaxWidth(\mb_strlen($cell->getValue()));
        }

        $this->rows[] = $row;

        return $this;
    }

    /**
     * Adds multiple rows in a batch.
     *
     * @param Row[]|array[] $rows
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
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
     * @param RendererContract|null $renderer Renderer to use. If none is given, DefaultRenderer is used.
     *
     * @throws ColumnKeyNotFoundException
     */
    public function render(?RendererContract $renderer = null): array
    {
        $renderer ??= new FancyRenderer();
        return $renderer->render($this);
    }

    /**
     * Returns rendered table as plain string, with all rows concatenated together using PHP_EOL
     * as line separator.
     *
     * @throws NoVisibleColumnsException
     * @throws ColumnKeyNotFoundException
     */
    public function renderAsString(): string
    {
        return \implode(\PHP_EOL, $this->render());
    }

    /**
     * @param string|int $columnKey Key of column to be obtained.
     *
     * @return Column
     *
     * @throws ColumnKeyNotFoundException
     */
    public function getColumn(string|int $columnKey): Column
    {
        return $this->columns->getColumn($columnKey);
    }

    public function hasColumn(string|int $columnKey): bool
    {
        return $this->columns->hasColumn($columnKey);
    }

    /**
     * Sets specified align to be default align for column header and column cells.
     *
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param Align      $align     Align to be set.
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnAlign(string|int $columnKey, Align $align): self
    {
        $this->setCellAlign($columnKey, $align);
        $this->setTitleAlign($columnKey, $align);

        return $this;
    }

    /**
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param Align      $align     Alignment to be set for the column cell's text
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setCellAlign(string|int $columnKey, Align $align): self
    {
        $this->getColumn($columnKey)->setCellAlign($align);
        return $this;
    }

    /**
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param Align      $align     Alignment to be set for the column's title text
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setTitleAlign(string|int $columnKey, Align $align): self
    {
        $this->getColumn($columnKey)->setTitleAlign($align);
        return $this;
    }

    /**
     * @param string|int $columnKey Unique column key to be assigned to this column
     * @param int        $width
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnMaxWidth(string|int $columnKey, int $width): self
    {
        $this->getColumn($columnKey)->setMaxWidth($width);

        return $this;
    }

    /**
     * @param string|int $columnKey
     * @param bool       $visible
     *
     * @return $this
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnVisibility(string|int $columnKey, bool $visible): self
    {
        $this->getColumn($columnKey)->setVisible($visible);

        return $this;
    }

    /**
     * @param array|string|int $columnKey Key of the column to be hidden. Hiding hidden column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function hideColumn(array|string|int $columnKey): self
    {
        $keys = \is_array($columnKey)
            ? $columnKey
            : (array)$columnKey;

        foreach ($keys as $key) {
            $this->setColumnVisibility($key, false);
        }

        return $this;
    }

    /**
     * @param string|int $columnKey Key of the column to be shown. Showing visible column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function showColumn(string|int $columnKey): self
    {
        $this->getColumn($columnKey)->setVisible(true);

        return $this;
    }

    /**
     * Returns number of columns with visibility set to `true`.
     */
    public function getVisibleColumnCount(): int
    {
        return \count(\array_filter($this->columns->toArray(), static fn(Column $column): bool => $column->isVisible()));
    }

}
