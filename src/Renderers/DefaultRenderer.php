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

namespace MarcinOrlowski\AsciiTable\Renderers;

use MarcinOrlowski\AsciiTable\Align;
use MarcinOrlowski\AsciiTable\AsciiTable;
use MarcinOrlowski\AsciiTable\Cell;
use MarcinOrlowski\AsciiTable\Column;
use MarcinOrlowski\AsciiTable\ColumnsContainer;
use MarcinOrlowski\AsciiTable\Exceptions\ColumnKeyNotFound;
use MarcinOrlowski\AsciiTable\Output\WriterContract;
use MarcinOrlowski\AsciiTable\Row;
use MarcinOrlowski\AsciiTable\Utils\StringUtils;

class DefaultRenderer implements RendererContract
{
    /**
     * @inheritDoc
     *
     * @throws ColumnKeyNotFound
     */
    public function render(AsciiTable $table, WriterContract $writer): void
    {
        $columns = $table->getColumns();

        $sep = $this->renderSeparator($columns);

        if (\count($columns) > 0) {
            $writer->write($sep);
            $writer->write($this->renderHeader($columns));
        }
        $writer->write($sep);
        $rows = $table->getRows();
        if (\count($rows) > 0) {
            foreach ($rows as $row) {
                /** @var Row $row */
                $writer->write($this->renderRow($columns, $row));
            }
        } else {
            $label = 'NO DATA';
            if (\mb_strlen($label) > $table->getTotalWidth()) {
                $label = \mb_substr($label, 0, $table->getTotalWidth() - 1) . '…';
            } else {
                $label = StringUtils::pad($label, $table->getTotalWidth(), ' ', \STR_PAD_BOTH);

            }
            $noData = \sprintf('| %s |' . PHP_EOL, $label);
            $writer->write($noData);
        }
        $writer->write($sep);
    }

    /* ****************************************************************************************** */

    /**
     * Render single data row.
     *
     * @param ColumnsContainer $columns Table column definition container.
     * @param Row              $row     Row to render
     *
     * @return string
     *
     * @throws ColumnKeyNotFound
     */
    protected function renderRow(ColumnsContainer $columns, Row $row): string
    {
        $result = '';
        $cells = $row->getContainer();
        foreach ($columns as $columnKey => $column) {
            if (!$column->isVisible()) {
                continue;
            }

            $cell = ($cells->has($columnKey))
                ? $cells->get($columnKey)
                : new Cell();

            if ($this->isFirstVisibleColumn($columns, $columnKey)) {
                $result .= self::HEADER_BORDER_LEFT;
            }

            // Using default column align
            $align = $cell->getAlign() === Align::AUTO
                ? $this->getColumnAlign($columns, $columnKey)
                : $cell->getAlign();

            $result .= $this->pad($columns, $columnKey, $cell->getValue(), $align);

            $result .= $this->isLastVisibleColumn($columns, $columnKey)
                ? self::HEADER_BORDER_RIGHT
                : self::HEADER_BORDER_CENTER;
        }

        $result .= PHP_EOL;

        return $result;
    }

    /* ****************************************************************************************** */

    protected const HEADER_BORDER_LEFT   = '| ';
    protected const HEADER_BORDER_CENTER = ' | ';
    protected const HEADER_BORDER_RIGHT  = ' |';

    /**
     * @param ColumnsContainer $columns Table column definition container.
     *
     * @return string
     *
     * @throws ColumnKeyNotFound
     */
    protected function renderHeader(ColumnsContainer $columns): string
    {
        $result = '';
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            if ($this->isFirstVisibleColumn($columns, $columnKey)) {
                $result .= self::HEADER_BORDER_LEFT;
            }

            $titleAlign = $column->getTitleAlign();
            $result .= $this->pad($columns, $columnKey, $column->getTitle(), $titleAlign);

            $result .= $this->isLastVisibleColumn($columns, $columnKey)
                ? self::HEADER_BORDER_RIGHT
                : self::HEADER_BORDER_CENTER;
        }

        $result .= PHP_EOL;

        return $result;
    }

    /* ****************************************************************************************** */

    protected function isFirstVisibleColumn(ColumnsContainer $columns, $columnKey): bool
    {
        foreach ($columns as $key => $column) {
            if ($column->isVisible()) {
                return $key === $columnKey;
            }
        }
        return false;
    }

    protected function isLastVisibleColumn(ColumnsContainer $columns, $columnKey): bool
    {
        $lastVisibleColumnKey = null;
        foreach ($columns as $key => $column) {
            if (!$column->isVisible()) {
                continue;
            }
            $lastVisibleColumnKey = $key;
        }
        return $lastVisibleColumnKey === $columnKey;
    }

    /* ****************************************************************************************** */

    protected const HEADER_SEGMENT_LEFT   = '+-';
    protected const HEADER_SEGMENT_CENTER = '-+-';
    protected const HEADER_SEGMENT_RIGHT  = '-+';

    /**
     * Renders separator row (usually to separate header/footer from
     * the table content).
     *
     * @param ColumnsContainer $columns Table column definition container.
     *
     * @return string
     */
    protected function renderSeparator(ColumnsContainer $columns): string
    {
        $result = '';
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if (!$column->isVisible()) {
                continue;
            }

            if ($this->isFirstVisibleColumn($columns, $columnKey)) {
                $result .= self::HEADER_SEGMENT_LEFT;
            }

            $result .= str_repeat('-', $column->getWidth());
            $result .= $this->isLastVisibleColumn($columns, $columnKey)
                ? self::HEADER_SEGMENT_RIGHT
                : self::HEADER_SEGMENT_CENTER;
        }

        $result .= PHP_EOL;

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
     * @throws ColumnKeyNotFound
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
     * @throws ColumnKeyNotFound
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
     * @throws ColumnKeyNotFound
     */
    protected function getColumnAlign(ColumnsContainer $columns, string|int $columnKey): Align
    {
        return $columns->getColumn($columnKey)->getDefaultColumnAlign();
    }

    /**
     * Helper method returning alignment of title string for colum referenced by `$columnKey`.
     *
     * @param ColumnsContainer $columns   Table column definition container.
     * @param string|int       $columnKey Column key we are going to populate.
     *
     * @throws ColumnKeyNotFound
     */
    protected function getColumnTitleAlign(ColumnsContainer $columns, string|int $columnKey): Align
    {
        return $columns->getColumn($columnKey)->getTitleAlign();
    }
    /* ****************************************************************************************** */

}
