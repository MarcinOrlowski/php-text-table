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
 * methods from Cell class:
 * @method string getValue()
 * @method Align getAlign()
 * @method self setAlign(Align $align)
 */
#[Getter]
#[Setter]
class CellNull extends Cell
{
    public function __construct(Align $align = Align::AUTO)
    {
        parent::__construct();

        $this->setValue('NULL');
        $this->setAlign($align);
    }

}
