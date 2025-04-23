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

/**
 * This class is used to store column's configuration.
 *
 * @method string getTitle()
 * @method Align getTitleAlign()
 * @method self setTitleAlign(Align $align)
 * @method Align getCellAlign()
 * @method self setCellAlign(Align $cellAlign)
 * @method int getMaxWidth()
 * @method self setMaxWidth(int $width)
 * @method self setVisible(bool $visible)
 * @method bool isVisible()
 * @method self setTitleVisible(bool $visible)
 * @method bool isTitleVisible()
 */
#[Getter]
#[Setter]
class Column extends \Lombok\Helper
{
    /**
     * @param string     $title        Column title string
     * @param Align      $align        Column text alignment. Shortcut to apply to both title and
     *                                 cell
     *                                 content.
     * @param int        $maxWidth     Max allowed width of the column. Content longer than
     *                                 `$maxWidth` will be automatically truncated
     * @param Align|null $cellAlign    Default column content alignment. Will be used for each cell
     *                                 in that column unless custom cell has own (non Align::AUTO)
     *                                 alignment specified. By default inherits from `$align`.
     * @param Align|null $titleAlign   Column title text alignment. By default inherits from
     *                                 `$align`.
     * @param bool       $visible      If set to `false`, column will be skipped when rendering
     *                                 table.
     * @param bool       $titleVisible If set to `false`, column title will be not rendered when
     *                                 rendering table.
     */
    public function __construct(string $title,
                                Align  $align = Align::AUTO,
                                int    $maxWidth = 0,
                                Align  $cellAlign = null,
                                Align  $titleAlign = null,
                                bool   $visible = true,
                                bool   $titleVisible = true)
    {
        parent::__construct();

        $this->setTitle($title);
        $this->setMaxWidth($maxWidth);
        $this->setAlign($align);
        if ($cellAlign !== null) {
            $this->setCellAlign($cellAlign);
        }
        if ($titleAlign !== null) {
            $this->setTitleAlign($titleAlign);
        }
        $this->setVisible($visible);
        $this->setTitleVisible($titleVisible);
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

    /**
     * Sets column title string.
     *
     * @param string $title Column title string
     *
     * @return $this Self reference for fluent interface.
     */
    protected function setTitle(string $title): self
    {
        $this->title = $title;
        $this->updateMaxWidth(\mb_strlen($title));

        return $this;
    }

    /* ****************************************************************************************** */

    /**
     * Determines visibility of visible column's title.
     */
    #[Getter, Setter]
    protected bool $titleVisible = true;

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

    /* ****************************************************************************************** */

    /**
     * Default column content alignment. Will be used for each cell in that column unless custom
     * cell has own (non Align::AUTO) alignment specified.
     */
    protected Align $cellAlign = Align::AUTO;

    /* ****************************************************************************************** */

    /**
     * Max allowed width of the column. Content longer than `$maxWidth` will be automatically
     * truncated
     */
    protected int $maxWidth = 0;

    /**
     * Updates max width of the column if given value is greater than current one.
     *
     * @param int $width New max width to be set.
     *
     * @return $this
     */
    public function updateMaxWidth(int $width): self
    {
        if ($width > $this->maxWidth) {
            $this->maxWidth = $width;
        }

        return $this;
    }

    /* ****************************************************************************************** */

    protected bool $visible = true;

}
