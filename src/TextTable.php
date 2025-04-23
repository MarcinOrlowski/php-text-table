<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   https://opensource.org/license/mit
 * @link      https://github.com/MarcinOrlowski/php-text-table
 */

namespace MarcinOrlowski\TextTable;

use Lombok\Getter;
use Lombok\Setter;
use MarcinOrlowski\TextTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\TextTable\Exceptions\DuplicateColumnKeyException;
use MarcinOrlowski\TextTable\Exceptions\NoVisibleColumnsException;
use MarcinOrlowski\TextTable\Exceptions\UnsupportedColumnTypeException;
use MarcinOrlowski\TextTable\Renderers\FancyRenderer;
use MarcinOrlowski\TextTable\Renderers\RendererContract;
use MarcinOrlowski\TextTable\Utils\StringUtils;

/**
 * @method ColumnsContainer getColumns()
 * @method RowsContainer getRows()
 * @method bool isHeaderVisible()
 * @method self setHeaderVisible(bool $headerVisible)
 * @method string getNoDataLabel()
 * @method self setNoDataLabel(string $noDataLabel)
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

    /**
     * String label to be shown when rendering empty table
     *
     * @var string
     */
    #[Getter, Setter]
    protected string $noDataLabel = 'NO DATA';

    /* ****************************************************************************************** */

    #[Getter, Setter]
    protected bool $headerVisible = true;

    /**
     * Enables table header (column names) rendering.
     */
    public function showHeader(): self
    {
        $this->setHeaderVisible(true);

        return $this;
    }

    /**
     * Disables table header (column names) rendering.
     */
    public function hideHeader(): self
    {
        $this->setHeaderVisible(false);

        return $this;
    }

    /* ****************************************************************************************** */

    /** Table column definitions and meta data */
    #[Getter]
    protected ColumnsContainer $columns;

    /**
     * Adds new column with specific key. Note columns are registered in the order they are
     * added.
     *
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column.
     * @param Column|string          $columnVal Either instance of `Column` or string to be used as
     *                                          column title (for which instance of `Column` will
     *                                          be automatically created).
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     * @throws DuplicateColumnKeyException
     * @throws UnsupportedColumnTypeException
     */
    public function addColumn(\Stringable|string|int $columnKey, Column|string $columnVal): self
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }
        $columnKey = StringUtils::sanitizeColumnKey($columnKey);
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
            // If column key is numeric, we assume it is an index and we need to auto-create
            // column key for it from the column title.
            if (\is_int($columnKey)) {
                if (\is_string($columnVal)) {
                    $columnKey = StringUtils::sanitizeColumnKey($columnVal);
                } else if ($columnVal instanceof Column) {
                    $columnKey = StringUtils::sanitizeColumnKey($columnVal->getTitle());
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
     * @param \Stringable|string|int $columnKey Key of column to be obtained.
     *
     * @return Column
     *
     * @throws ColumnKeyNotFoundException
     */
    public function getColumn(\Stringable|string|int $columnKey): Column
    {
        return $this->columns->getColumn($columnKey);
    }

    /**
     * Returns `TRUE` if column referenced by specified key exists, `FALSE` otherwise.
     *
     * @param \Stringable|string|int $columnKey Column key or index to be checked.
     *
     * @return bool `TRUE` if column exists, `FALSE` otherwise.
     */
    public function hasColumn(\Stringable|string|int $columnKey): bool
    {
        return $this->columns->hasColumn($columnKey);
    }

    /**
     * Sets specified align to be default align for column header and column cells.
     *
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column
     * @param Align                  $align     Align to be set.
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnAlign(\Stringable|string|int $columnKey, Align $align): self
    {
        $this->setCellAlign($columnKey, $align);
        $this->setTitleAlign($columnKey, $align);

        return $this;
    }

    /**
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column
     * @param Align                  $align     Alignment to be set for the column cell's text
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setCellAlign(\Stringable|string|int $columnKey, Align $align): self
    {
        $this->getColumn($columnKey)->setCellAlign($align);

        return $this;
    }

    /**
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column
     * @param Align                  $align     Alignment to be set for the column's title text
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setTitleAlign(\Stringable|string|int $columnKey, Align $align): self
    {
        $this->getColumn($columnKey)->setTitleAlign($align);

        return $this;
    }

    /**
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column
     * @param int                    $width     Max width to be set for the column
     *
     * @return self
     *
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnMaxWidth(\Stringable|string|int $columnKey, int $width): self
    {
        $this->getColumn($columnKey)->setMaxWidth($width);

        return $this;
    }

    /**
     * @param \Stringable|string|int $columnKey Unique column key to be assigned to this column
     * @param bool                   $visible   `TRUE` to make column visible, `FALSE` to hide it.
     *
     * @return $this
     * @throws ColumnKeyNotFoundException
     */
    public function setColumnVisibility(\Stringable|string|int $columnKey, bool $visible): self
    {
        $this->getColumn($columnKey)->setVisible($visible);

        return $this;
    }

    /**
     * @param array|\Stringable|string|int $columnKey Key of the column to be hidden. Hiding hidden
     *                                                column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function hideColumn(array|\Stringable|string|int $columnKey): self
    {
        if ($columnKey instanceof \Stringable) {
            $columnKey = $columnKey->__toString();
        }

        $keys = \is_array($columnKey)
            ? $columnKey
            : (array)$columnKey;

        foreach ($keys as $key) {
            $this->setColumnVisibility($key, false);
        }

        return $this;
    }

    /**
     * @param \Stringable|string|int $columnKey Key of the column to be shown. Showing visible
     *                                          column has no effect.
     *
     * @return $this
     *
     * @throws ColumnKeyNotFoundException
     */
    public function showColumn(\Stringable|string|int $columnKey): self
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

    /**
     * Adds horizontal separator line to the table
     */
    public function addSeparator(): self
    {
        $this->addRow(new Separator());

        return $this;
    }

    /**
     * Get total width of the visible table columns' content (note this does not include column
     * padding, nor separators etc as this is not part of the table data but belongs to renderer).
     */
    public function getContentTotalWidth(): int
    {
        $totalWidth = 0;

        foreach ($this->getColumns() as $column) {
            /** @var Column $column */
            if ($column->isVisible()) {
                $totalWidth += $column->getWidth();
            }
        }

        return $totalWidth;
    }
}
