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

class Column
{
    public function __construct(string $title, Span|int $width = Span::AUTO, Align $align = Align::AUTO)
    {
        $this->setTitle($title);
        $this->setWidth($width);
        $this->setAlign($align);
    }

    /* ****************************************************************************************** */

    protected string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    protected function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updateMaxWidth(\strlen($title));

        return $this;
    }

    /* ****************************************************************************************** */

    protected Span|int $width;

    public function getWidth(): int
    {
        // FIXME Span support
        return $this->getMaxWidth();
    }

    protected function setWidth(Span|int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /* ****************************************************************************************** */

    protected Align $padding = Align::AUTO;

    public function getAlign(): Align
    {
        return $this->padding;
    }

    protected function setAlign(Align $align): self
    {
        $this->padding = $align;
        return $this;
    }

    /* ****************************************************************************************** */

    protected int $maxWidth = 0;

    protected function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function updateMaxWidth(Span|int $width): self
    {
        if ($width instanceof Span) {
            throw new \InvalidArgumentException('Not supported yet');
        }

        if ($width > $this->maxWidth) {
            $this->maxWidth = $width;
        }
        return $this;
    }

}
