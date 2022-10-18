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
    public function __construct(string $title,
                                Align  $align = Align::AUTO,
                                int    $maxWidth = 0,
                                Align  $titleAlign = Align::AUTO)
    {
        $this->setTitle($title);
        $this->setMaxWidth($maxWidth);
        $this->setDefaultColumnAlign($align);
        $this->setTitleAlign($titleAlign);
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

    /**
     * Default column content alignment. Will be used for each cell in that column unless custom
     * content alignment is set up.
     */
    protected Align $defaultColumnAlign = Align::AUTO;

    public function getDefaultColumnAlign(): Align
    {
        return $this->defaultColumnAlign;
    }

    public function setDefaultColumnAlign(Align $defaultColumnAlign): self
    {
        $this->defaultColumnAlign = $defaultColumnAlign;
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

}
