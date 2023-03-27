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

use MarcinOrlowski\TextTable\Align;
use MarcinOrlowski\TextTable\Cell;
use MarcinOrlowski\TextTable\Column;
use MarcinOrlowski\TextTable\ColumnsContainer;
use MarcinOrlowski\TextTable\Exceptions\ColumnKeyNotFoundException;
use MarcinOrlowski\TextTable\Exceptions\NoVisibleColumnsException;
use MarcinOrlowski\TextTable\Row;
use MarcinOrlowski\TextTable\TextTable;
use MarcinOrlowski\TextTable\Utils\StringUtils;

abstract class AsciiTableRenderer implements RendererContract
{
    /**
     * @inheritDoc
     *
     * @throws ColumnKeyNotFoundException
     */
    public function render(TextTable $table): array
    {
        $ctx = new RenderContext($table);

        // Ensure we have at least one column visible
        if ($ctx->getTable()->getVisibleColumnCount() === 0) {
            throw new NoVisibleColumnsException();
        }

        $result = [];

        $result[] = $this->renderTopSeparator($ctx);
        $result[] = $this->renderHeader($ctx);
        $result[] = $this->renderSeparator($ctx);
        if ($table->getRowCount() > 0) {
            foreach ($table->getRows() as $row) {
                /** @var Row $row */
                $result[] = $this->renderDataRow($ctx, $row);
            }
        } else {
            $result[] = $this->renderNoDataRow($ctx);
            $ctx->incRenderedRowIdx();
        }
        $result[] = $this->renderBottomSeparator($ctx);

        return $result;
    }

    /**
     * @inheritDoc
     *
     * @throws ColumnKeyNotFoundException
     */
    public function renderAsString(TextTable $table): string
    {
        return \implode(PHP_EOL, $this->render($table));
    }

    protected function renderNoDataRow(RenderContext $ctx): string
    {
        $table = $ctx->getTable();

        $label = 'NO DATA';
        $tableTotalWidth = $this->getTableTotalWidth($table);
        $label = (\mb_strlen($label) > $tableTotalWidth)
            ? \mb_substr($label, 0, $tableTotalWidth - 1) . '…'
            : StringUtils::pad($label, $tableTotalWidth, ' ', \STR_PAD_BOTH);
        return static::ROW_FRAME_LEFT . $label . static::ROW_FRAME_RIGHT;
    }

    /* ****************************************************************************************** */

    public const ROW_FRAME_LEFT   = '?';
    public const ROW_FRAME_CENTER = '?';
    public const ROW_FRAME_RIGHT  = '?';

    /**
     * Render single data row.
     *
     * @param RenderContext $ctx Rendering context object.
     * @param Row           $row Row to render
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function renderDataRow(RenderContext $ctx, Row $row): string
    {
        $result = '';

        $columns = $ctx->getTable()->getColumns();
        $cells = $row->getContainer();
        foreach ($columns as $columnKey => $column) {
            if (!$column->isVisible()) {
                continue;
            }

            $cell = ($cells->hasCell($columnKey))
                ? $cells->getCell($columnKey)
                : new Cell();

            if ($ctx->isFirstVisibleColumn($columnKey)) {
                $result .= static::ROW_FRAME_LEFT;
            }

            // Using default column align
            $align = $cell->getAlign() === Align::AUTO
                ? $this->getColumnAlign($columns, $columnKey)
                : $cell->getAlign();

            $result .= $this->pad($columns, $columnKey, $cell->getValue(), $align);

            $result .= $ctx->isLastVisibleColumn($columnKey)
                ? static::ROW_FRAME_RIGHT
                : static::ROW_FRAME_CENTER;
        }

        $ctx->incRenderedRowIdx()->incTableRowIdx();

        return $result;
    }

    /* ****************************************************************************************** */

    /**
     * @param RenderContext $ctx Rendering context object.
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function renderHeader(RenderContext $ctx): string
    {
        $columns = $ctx->getTable()->getColumns();

        $result = '';
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            if ($ctx->isFirstVisibleColumn($columnKey)) {
                $result .= static::ROW_FRAME_LEFT;
            }

            $result .= $this->pad($columns, $columnKey, $column->getTitle(), $column->getTitleAlign());
            $result .= $ctx->isLastVisibleColumn($columnKey)
                ? static::ROW_FRAME_RIGHT
                : static::ROW_FRAME_CENTER;
        }

        // Do not increment data row index for header rows!
        $ctx->incRenderedRowIdx();

        return $result;
    }

    /* ****************************************************************************************** */

    public const SEGMENT_ROW_FILL         = '?';
    public const SEGMENT_FIRST_ROW_LEFT   = '?';
    public const SEGMENT_FIRST_ROW_CENTER = '?';
    public const SEGMENT_FIRST_ROW_RIGHT  = '?';
    public const SEGMENT_ROW_LEFT         = '?';
    public const SEGMENT_ROW_CENTER       = '?';
    public const SEGMENT_ROW_RIGHT        = '?';
    public const SEGMENT_LAST_ROW_LEFT    = '?';
    public const SEGMENT_LAST_ROW_CENTER  = '?';
    public const SEGMENT_LAST_ROW_RIGHT   = '?';


    protected function renderBottomSeparator(RenderContext $ctx): string
    {
        $columns = $ctx->getTable()->getColumns();
        $result = static::SEGMENT_LAST_ROW_LEFT;
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            $result .= \str_repeat(static::SEGMENT_ROW_FILL, $column->getWidth());
            $result .= $ctx->isLastVisibleColumn($columnKey)
                ? static::SEGMENT_LAST_ROW_RIGHT
                : static::SEGMENT_LAST_ROW_CENTER;
        }

        $ctx->incRenderedRowIdx();
        return $result;
    }

    protected function renderTopSeparator(RenderContext $ctx): string
    {
        $columns = $ctx->getTable()->getColumns();
        $result = static::SEGMENT_FIRST_ROW_LEFT;
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            $result .= \str_repeat(static::SEGMENT_ROW_FILL, $column->getWidth());
            $result .= $ctx->isLastVisibleColumn($columnKey)
                ? static::SEGMENT_FIRST_ROW_RIGHT
                : static::SEGMENT_FIRST_ROW_CENTER;
        }

        $ctx->incRenderedRowIdx();
        return $result;
    }

    /**
     * Renders separator row (usually to separate header/footer from
     * the table content). To render top/bottom most edge separators
     * use {@see renderTopSeparator()} and {@see renderBottomSeparator()}
     *
     * @param RenderContext $ctx Rendering context object.
     */
    protected function renderSeparator(RenderContext $ctx): string
    {
        $columns = $ctx->getTable()->getColumns();

        $result = '';

        // check if table is empty (otherwise islastvisiblerow() will return
        // true as row 0 is the last of 0 row dataset, rendering last closing table
        // row characters instead of first row characters) which is visible for
        // any non-symetric blocks (i.e. MsDos style blocks)
        if ($ctx->getTable()->getRowCount() > 0) {
            if ($ctx->isLastVisibleRow()) {
                $isFirstRow = false;
                $isLastRow = true;
            } elseif ($ctx->isFirstVisibleRow()) {
                $isFirstRow = true;
                $isLastRow = false;
            } else {
                $isFirstRow = false;
                $isLastRow = false;
            }
        } else {
            //
        }

        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            /// FIXME - hide first column and we a fucked
            if ($isFirstRow) {
                $segment = static::SEGMENT_FIRST_ROW_LEFT;
            } elseif ($isLastRow) {
                $segment = static::SEGMENT_LAST_ROW_LEFT;
            } else {
                $segment = static::SEGMENT_ROW_LEFT;
            }
            if ($ctx->isFirstVisibleColumn($columnKey)) {
                $result .= $segment;
            }

            $result .= \str_repeat(static::SEGMENT_ROW_FILL, $column->getWidth());

            if ($ctx->isLastVisibleColumn($columnKey)) {
                if ($isFirstRow) {
                    $segment = static::SEGMENT_FIRST_ROW_RIGHT;
                } elseif ($isLastRow) {
                    $segment = static::SEGMENT_LAST_ROW_RIGHT;
                } else {
                    $segment = static::SEGMENT_ROW_RIGHT;
                }
            } else {
                if ($isFirstRow) {
                    $segment = static::SEGMENT_FIRST_ROW_CENTER;
                } elseif ($isLastRow) {
                    $segment = static::SEGMENT_LAST_ROW_CENTER;
                } else {
                    $segment = static::SEGMENT_ROW_CENTER;
                }
            }
            $result .= $segment;
        }

        $ctx->incRenderedRowIdx();

        return $result;
    }

    /* ****************************************************************************************** */

    /**
     * Pads given $value to fit column allowed width. If `$value` would exceed max allowed
     * width, it will be truncated to fit. Returns aligned string.
     *
     * @param ColumnsContainer $columns   Table column definition container.
     * @param string|int       $columnKey Column key we are going to populate.
     * @param string           $value     Value to pad.
     * @param Align|null       $align     Requested text alignment. If null, column's alignment will be used.
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function pad(ColumnsContainer $columns,
                           string|int       $columnKey,
                           string           $value,
                           ?Align           $align = null): string
    {
        // If no custom align specified, inherit column's default align.
        $align ??= $this->getColumnAlign($columns, $columnKey);
        $maxWidth = $this->getColumnWidth($columns, $columnKey);

        $padType = match ($align) {
            Align::RIGHT => \STR_PAD_LEFT,
            Align::LEFT => \STR_PAD_RIGHT,
            Align::CENTER => \STR_PAD_BOTH,
            Align::AUTO => \STR_PAD_RIGHT,
        };

        $strLen = \mb_strlen($value);

        // Clip the string if it is longer that max allowed column width
        if ($strLen > $maxWidth) {
            $value = \mb_substr($value, 0, $maxWidth - 1, 'utf-8') . '…';
        }

        return StringUtils::pad($value, $maxWidth, ' ', $padType);
    }

    /* ****************************************************************************************** */

    /**
     * Helper method returning width of column referenced by `$columnKey`.
     *
     * @param ColumnsContainer $columns   Table column definition container.
     * @param string|int       $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function getColumnWidth(ColumnsContainer $columns, string|int $columnKey): int
    {
        return $columns->getColumn($columnKey)->getWidth();
    }

    /**
     * Helper method returning alignment of column referenced by `$columnKey`.
     *
     * @param ColumnsContainer $columns   Table column definition container.
     * @param string|int       $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function getColumnAlign(ColumnsContainer $columns, string|int $columnKey): Align
    {
        return $columns->getColumn($columnKey)->getCellAlign();
    }

    /**
     * Helper method returning alignment of title string for colum referenced by `$columnKey`.
     *
     * @param ColumnsContainer $columns   Table column definition container.
     * @param string|int       $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFoundException
     */
    protected function getColumnTitleAlign(ColumnsContainer $columns, string|int $columnKey): Align
    {
        return $columns->getColumn($columnKey)->getTitleAlign();
    }

    /* ****************************************************************************************** */

    /**
     * Get total width of the visible table WITHOUT edges (so the full table width
     * in output is then 2 (for left edge) + getTotalWidth() + 2 (for right edge).
     */
    protected function getTableTotalWidth(TextTable $table): int
    {
        $totalWidth = 0;

        foreach ($table->getColumns() as $column) {
            /** @var Column $column */
            if ($column->isVisible()) {
                $totalWidth += $column->getWidth();
            }
        }

        // Next, we need to account column separators as well.
        $totalWidth += ($table->getVisibleColumnCount() - 1) * \mb_strlen(static::ROW_FRAME_CENTER);

        return $totalWidth;
    }

    /* ****************************************************************************************** */

}
