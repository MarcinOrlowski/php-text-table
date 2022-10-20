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

class Column
{
    public function __construct(string $title,
                                Align  $align = Align::AUTO,
                                int    $maxWidth = 0,
                                Align  $cellAlign = Align::AUTO,
                                Align  $titleAlign = Align::AUTO,
                                bool   $visible = true)
    {
        $this->setTitle($title);
        $this->setMaxWidth($maxWidth);
        $this->setAlign($align);
        $this->setCellAlign($cellAlign);
        $this->setTitleAlign($titleAlign);
        $this->setVisibility($visible);
    }

    /* ****************************************************************************************** */

    /**
     * Returns current column's content width.
     */
    public function getWidth(): int
    {
        return $this->getMaxWidth();
    }

    /* ****************************************************************************************** */

    /** Column title string */
    protected string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    protected function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updateMaxWidth(\mb_strlen($title));

        return $this;
    }

    /* ****************************************************************************************** */

    /**
     * Sets align for both column title and cell content.
     *
     * @param Align $align Align to set.
     *
     * @return $this
     */
    public function setAlign(Align $align): self
    {
        $this->setCellAlign($align);
        $this->setTitleAlign($align);
        return $this;
    }

    /* ****************************************************************************************** */

    /** Column title text alignment */
    protected Align $titleAlign = Align::LEFT;

    public function getTitleAlign(): Align
    {
        return $this->titleAlign;
    }

    /**
     * Sets align for column title text.
     *
     * @param Align $align Align to set.
     *
     * @return $this
     */
    public function setTitleAlign(Align $align): self
    {
        $this->titleAlign = $align;

        return $this;
    }

    /* ****************************************************************************************** */

    /**
     * Default column content alignment. Will be used for each cell in that column unless custom
     * cell has own (non Align::AUTO) alignment specified.
     */
    protected Align $columnAlign = Align::AUTO;

    public function getColumnAlign(): Align
    {
        return $this->columnAlign;
    }

    public function setCellAlign(Align $columnAlign): self
    {
        $this->columnAlign = $columnAlign;
        return $this;
    }

    /* ****************************************************************************************** */

    /** Max allowed width of the column. Content longer than `$maxWidth` will be automatically truncated */
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

    /* ****************************************************************************************** */

    protected bool $visible = true;

    public function isVisible(): bool
    {
        return $this->visible;
    }

    /** @deprecated */
    public function hide(): self
    {
        return $this->setVisibility(false);
    }

    /** @deprecated */
    public function show(): self
    {
        return $this->setVisibility(true);
    }

    public function setVisibility(bool $visible): self
    {
        $this->visible = $visible;
        return $this;
    }

}
