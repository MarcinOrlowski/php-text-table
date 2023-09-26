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
     * @param string[]|Column[] $headerColumns Optional array of column headers to be created.
     * @param Row[]|array[]     $rows          Optional row lib list of data to be added as table
     *                                         rows.
     *
     * NOTE: if you want to add single row with columns data provided as list (i.e. new
     * TextTable(..., [1, 2, 3])), you MUST wrap it in another `array`: new TextTable(..., [[1, 2,
     * 3]]) otherwise your row data will be treated as *array of rows*, and not as array of column
     * data, which will lead to runtime errors.
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
     */
    public function __construct(array $headerColumns = [], array $rows = [])
    {
        parent::__construct();
        $this->init($headerColumns, $rows);
    }

    /**
     * @param string[]|Column[] $headerColumns Optional array of column headers to be created.
     * @param Row[]|array[]     $rows          Optional row lib list of data to be added as table
     *                                         rows.
     *
     * NOTE: if you want to add single row with columns data provided as list (i.e. new
     * TextTable(..., [1, 2, 3])), you MUST wrap it in another `array`: new TextTable(..., [[1, 2,
     * 3]]) otherwise your row data will be treated as *array of rows*, and not as array of column
     * data, which will lead to runtime errors.
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
     */
    public function init(array $headerColumns = [], array $rows = []): self
    {
        $this->columns = new ColumnsContainer();
        $this->rows = new RowsContainer();
        $this->addColumns($headerColumns);
        $this->addRows($rows);

        return $this;
    }

    /* ****************************************************************************************** */

    /** Table column definitions and meta data */
    #[Getter]
    protected ColumnsContainer $columns;

    /**
     * Adds new column with specific index. Note columns are registered in the order they are
     * added.
     *
     * @param string|int    $columnKey Unique column key to be assigned to this column.
     * @param Column|string $columnVal Either instance of `Column` or string to be used as column
     *                                 title (for which instance of `Column` will be automatically
     *                                 created).
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
            throw UnsupportedColumnTypeException::forColumnKeyVal($columnKey, $columnVal);
        }

        $this->columns->addColumn($columnKey, $columnVal);

        return $this;
    }

    /**
     * Adds multiple columns at once. Note columns are registered in the order they are present in
     * source array.
     *
     * Note that this method **auto-creates** column keys for all **non-string** table keys. For
     * such case the key will be derived either from passed `string` for from `Column` instance'
     * title string. All explicitly specified `string` keys will be preserved and used.
     *
     * @param string[]|Column[] $columns List of columns to be added, given either via instance of
     *                                   `Column` or as string to be used as column title (for
     *                                   which instance of `Column` will be automatically created).
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

    public function getColumnCount(): int
    {
        return \count($this->getColumns());
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
     * @param Row|array|null $srcRow When array is given, it is expected that each of the columns
     *                               represents the row's cell value. The elements should be either
     *                               instances of the Cell class or string|int. If a primitive is
     *                               given, an instance of Cell will automatically be created. The
     *                               array elements can be in the form columnKey => value or can be
     *                               provided without their own keys. In the latter case, the
     *                               proper columnKey will be selected based on the table column
     *                               definitions (i.e. for $srcRow being ['a', 'b'], the b value
     *                               will be placed into the cell of the second column). This
     *                               auto-assignment works only when $srcRow array uses no custom
     *                               keys and table column definitions all use string keys. In all
     *                               other cases, an explicit column key must be specified. Passing
     *                               null as $srcRow has no effect.
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

        $itemsToAddCount = \count($srcRow);

        $row = $srcRow;
        if (\is_array($srcRow)) {
            $row = new Row();

            // If source array has only numeric keys, and column definitions are using non-numeric keys,
            // then it is assumed that source array elements are in sequence and will be automatically
            // assigned to cell at position matching their index in source array.
            $srcHasNumKeysOnly = $itemsToAddCount === \count(\array_filter(\array_keys($srcRow), \is_int(...)));
            $columnsHaveStringKeysOnly = \count($columns) === \count(
                    \array_filter(\array_keys($columns->toArray()), \is_string(...)));

            if ($srcHasNumKeysOnly && $columnsHaveStringKeysOnly) {
                $columnKeys = \array_keys($columns->toArray());

                $srcIdx = 0;
                foreach ($srcRow as $cell) {
                    $columnKey = $columnKeys[$srcIdx];
                    $row->addCell($columnKey, $cell);
                    $srcIdx++;

                    if ($srcIdx >= $this->getColumnCount()) {
                        // FIXME: add option to configure TextTable to throw in such case.
                        break;
                    }
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

        $this->rows[$this->getRowCount()] = $row;

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
     * @param RendererContract|null $renderer Renderer to use. If none is given, default renderer
     *                                        is used.
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
     * @param array|string|int $columnKey Key of the column to be hidden. Hiding hidden column has
     *                                    no effect.
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
     * @param string|int $columnKey Key of the column to be shown. Showing visible column has no
     *                              effect.
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
