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

use MarcinOrlowski\TextTable\Traits\ArrayableTrait;
use MarcinOrlowski\TextTable\Traits\ArrayAccessTrait;
use MarcinOrlowski\TextTable\Traits\CountableTrait;
use MarcinOrlowski\TextTable\Traits\IteratorAggregateTrait;

abstract class BaseContainer extends \Lombok\Helper implements ContainerContract
{
    use ArrayAccessTrait;
    use ArrayableTrait;
    use CountableTrait;
    use IteratorAggregateTrait;
}
