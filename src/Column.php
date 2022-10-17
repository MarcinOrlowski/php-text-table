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
    public function __construct(string $title, Align $align = Align::AUTO, int $maxWidth = 0)
    {
        $this->setTitle($title);
        $this->setMaxWidth($maxWidth);
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

    protected Align $titleAlign = Align::LEFT;

    public function getTitleAlign(): Align
    {
        return $this->titleAlign;
    }

    public function setTitleAlign(Align $align): self
    {
        $this->titleAlign = $align;

        return $this;
    }

    /* ****************************************************************************************** */

    protected int $width = 0;

    public function getWidth(): int
    {
        // FIXME Width support
        return $this->getMaxWidth();
    }

    protected function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /* ****************************************************************************************** */

    protected Align $align = Align::AUTO;

    public function getAlign(): Align
    {
        return $this->align;
    }

    public function setAlign(Align $align): self
    {
        $this->align = $align;
        return $this;
    }

    /* ****************************************************************************************** */

    protected int $maxWidth = 0;

    protected function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function setMaxWidth(int $width): self
    {
        $this->maxWidth = $width;
        return $this;
    }

    public function updateMaxWidth(int $width): self
    {
        if ($width > $this->maxWidth) {
            $this->maxWidth = $width;
        }
        return $this;
    }

}
