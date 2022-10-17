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

class Renderer
{
    /* ****************************************************************************************** */

    public function render(AsciiTable $table, OutputContract $writer): void
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
            $writer->write('NO DATA');
        }
        $writer->write($sep);
    }

    /* ****************************************************************************************** */

    protected function renderRow(ColumnsContainer $columns, Row $row): string
    {
        $result = '';
        $cells = $row->getCells();
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

            $result .= $this->pad($columns, $columnKey, $cell->getValue());

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

            $result .= $this->pad($columns, $columnKey, $column->getTitle());

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
        foreach ($columns as $columnKey => $column) {
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

    protected function pad(ColumnsContainer $columns, string|int $columnKey, string $value): string
    {
        $width = $this->getColumnWidth($columns, $columnKey, $value);
        $align = $this->getColumnAlign($columns, $columnKey);
        return $this->padRaw($value, $width, $align);
    }

    protected function padRaw(string $string,
                              int    $minWidth,
                              Align  $align = Align::RIGHT,
                              string $padding = ' '): string
    {
        $padType = match ($align) {
            Align::LEFT => \STR_PAD_RIGHT,
            Align::RIGHT => \STR_PAD_LEFT,
            Align::CENTER => \STR_PAD_BOTH,
            Align::AUTO => \STR_PAD_RIGHT,
        };

        return \str_pad($string, \max(\strlen($string), $minWidth), $padding, $padType);
    }

    /* ****************************************************************************************** */

    protected function getColumnWidth(ColumnsContainer $columns, string|int $columnIdx, string $value): int
    {
        $columnMeta = $columns->get($columnIdx);
        return \max($columnMeta->getWidth(), \strlen($value));
    }

    protected function getColumnAlign(ColumnsContainer $columns, string|int $columnIdx): Align
    {
        return $columns->get($columnIdx)->getAlign();
    }

    /* ****************************************************************************************** */

}
