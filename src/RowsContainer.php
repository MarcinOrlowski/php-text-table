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

/**
 * Container class holding all the table's rows.
 */
class RowsContainer extends BaseContainer
{
    /** @var Row[] $container */
    protected mixed $container = [];
}
