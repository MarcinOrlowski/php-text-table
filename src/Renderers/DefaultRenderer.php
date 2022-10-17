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
            if (\strlen($label) > $table->getTotalWidth()) {
                $label = \substr($label, 0, $table->getTotalWidth() - 1) . '…';
            } else {
                $label = \str_pad($label, $table->getTotalWidth(), ' ', \STR_PAD_BOTH);

            }
            $noData = \sprintf('| %s |', $label);
            $writer->write($noData);
        }
        $writer->write($sep);
    }

    /* ****************************************************************************************** */

    /**
     * @param ColumnsContainer $columns
     * @param Row              $row Row to render
     *
     * @return string
     *
     * @throws ColumnKeyNotFound
     */
    protected function renderRow(ColumnsContainer $columns, Row $row): string
    {
        $result = '';
        $cells = $row->getContainer();
        $cnt = \count($cells);
        $columnOffset = 0;
        foreach (\array_keys($columns->toArray()) as $columnKey) {
            $cell = $cells->get($columnKey);

            /**
             * @var string|int $columnKey
             * @var Cell       $cell
             */
            if ($columnOffset === 0) {
                $result .= self::HEADER_PAD_LEFT;
            }

            // Using default column align
            $align = $cell->getAlign() === Align::AUTO
                ? $this->getColumnAlign($columns, $columnKey)
                : $cell->getAlign();

            $result .= $this->pad($columns, $columnKey, $cell->getValue(), $align);

            $result .= ($columnOffset === $cnt - 1)
                ? self::HEADER_PAD_RIGHT
                : self::HEADER_PAD_CENTER;

            $columnOffset++;
        }

        $result .= PHP_EOL;

        return $result;
    }

    /* ****************************************************************************************** */

    protected const HEADER_PAD_LEFT   = '| ';
    protected const HEADER_PAD_CENTER = ' | ';
    protected const HEADER_PAD_RIGHT  = ' |';

    /**
     * @param ColumnsContainer $columns
     *
     * @return string
     *
     * @throws ColumnKeyNotFound
     */
    protected function renderHeader(ColumnsContainer $columns): string
    {
        $result = '';
        $cnt = \count($columns);
        $columnOffset = 0;
        foreach ($columns as $columnKey => $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if ($columnOffset === 0) {
                $result .= self::HEADER_PAD_LEFT;
            }

            $titleAlign = $this->getColumnTitleAlign($columns, $columnKey);
            $result .= $this->pad($columns, $columnKey, $column->getTitle(), $titleAlign);

            $result .= ($columnOffset === $cnt - 1)
                ? self::HEADER_PAD_RIGHT
                : self::HEADER_PAD_CENTER;

            $columnOffset++;
        }

        $result .= PHP_EOL;

        return $result;
    }

    /* ****************************************************************************************** */

    protected const HEADER_SEGMENT_LEFT   = '+-';
    protected const HEADER_SEGMENT_CENTER = '-+-';
    protected const HEADER_SEGMENT_RIGHT  = '-+';

    protected function renderSeparator(ColumnsContainer $columns): string
    {
        $result = '';
        $cnt = \count($columns);
        $columnOffset = 0;
        foreach ($columns as $column) {
            /**
             * @var string|int $columnKey
             * @var Column     $column
             */
            if ($columnOffset === 0) {
                $result .= self::HEADER_SEGMENT_LEFT;
            }

            $result .= str_repeat('-', $column->getWidth());

            $result .= ($columnOffset === $cnt - 1)
                ? self::HEADER_SEGMENT_RIGHT
                : self::HEADER_SEGMENT_CENTER;

            $columnOffset++;
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

        $strLen = \strlen($value);

        // Clip the string if it is longer that max allowed column width
        if ($strLen > $maxWidth) {
            $value = \substr($value, 0, $maxWidth - 1) . '…';
        }

        return \str_pad($value, $maxWidth, ' ', $padType);
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
        return $columns->get($columnKey)->getWidth();
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
        return $columns->get($columnKey)->getDefaultColumnAlign();
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
        return $columns->get($columnKey)->getTitleAlign();
    }
    /* ****************************************************************************************** */

}
