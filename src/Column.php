<?php
declare(strict_types=1);

/**
 * Text Table
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
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
 */
#[Getter]
#[Setter]
class Column extends \Lombok\Helper
{
    /**
     * @param string $title      Column title string
     * @param Align  $align      Column text alignment. Shortcut to apply to both title and cell
     *                           content.
     * @param int    $maxWidth   Max allowed width of the column. Content longer than `$maxWidth`
     *                           will be automatically truncated
     * @param Align  $cellAlign  Default column content alignment. Will be used for each cell in
     *                           that column unless custom cell has own (non Align::AUTO) alignment
     *                           specified.
     * @param Align  $titleAlign Column title text alignment
     * @param bool   $visible    If set to `false`, column will be skipped when rendering table.
     */
    public function __construct(string $title,
                                Align  $align = Align::AUTO,
                                int    $maxWidth = 0,
                                Align  $cellAlign = Align::AUTO,
                                Align  $titleAlign = Align::AUTO,
                                bool   $visible = true)
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
