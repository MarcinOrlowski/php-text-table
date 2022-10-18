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

use MarcinOrlowski\AsciiTable\Traits\ArrayAccessTrait;
use MarcinOrlowski\AsciiTable\Traits\IteratorAggregateTrait;

class RowsContainer implements \Countable, \IteratorAggregate, \ArrayAccess, ArrayableContract
{
    use ArrayAccessTrait;
    use IteratorAggregateTrait;

    /** @var Row[] $container */
    protected array $container = [];

}
